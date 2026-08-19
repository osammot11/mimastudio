<?php

namespace Tests\Feature;

use App\Mail\ClientWorkReady;
use App\Models\Client;
use App\Models\ClientImage;
use App\Models\Customer;
use App\Models\GalleryUploadSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        config(['gallery.min_free_bytes' => 0]);
    }

    public function test_admin_can_create_a_manifest_with_one_thousand_images(): void
    {
        [$user, $client] = $this->userAndClient();
        $manifest = collect(range(0, 999))->map(fn (int $position) => [
            'fingerprint' => hash('sha256', "image-{$position}"),
            'name' => "image-{$position}.jpg",
            'size' => 1024,
            'position' => $position,
        ])->all();

        $this->actingAs($user)
            ->postJson(route('admin.clients.gallery-uploads.store', $client), [
                'manifest' => $manifest,
            ])
            ->assertCreated()
            ->assertJsonPath('expected_files', 1000)
            ->assertJsonCount(0, 'uploaded_fingerprints');

        $this->assertDatabaseCount('gallery_upload_items', 1000);
    }

    public function test_manifest_rejects_more_than_one_thousand_images(): void
    {
        [$user, $client] = $this->userAndClient();
        $manifest = collect(range(0, 1000))->map(fn (int $position) => [
            'fingerprint' => hash('sha256', "image-{$position}"),
            'name' => "image-{$position}.jpg",
            'size' => 1,
            'position' => $position,
        ])->all();

        $this->actingAs($user)
            ->postJson(route('admin.clients.gallery-uploads.store', $client), [
                'manifest' => $manifest,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('manifest');
    }

    public function test_manifest_rejects_an_image_over_four_megabytes(): void
    {
        [$user, $client] = $this->userAndClient();

        $this->actingAs($user)
            ->postJson(route('admin.clients.gallery-uploads.store', $client), [
                'manifest' => [[
                    'fingerprint' => hash('sha256', 'too-large'),
                    'name' => 'too-large.jpg',
                    'size' => (4 * 1024 * 1024) + 1,
                    'position' => 0,
                ]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('manifest.0.size');
    }

    public function test_batches_are_resumable_idempotent_and_only_visible_after_completion(): void
    {
        [$user, $client] = $this->userAndClient();
        $firstFile = UploadedFile::fake()->image('first.jpg', 1200, 800);
        $secondFile = UploadedFile::fake()->image('second.jpg', 800, 1200);
        $files = [$firstFile, $secondFile];
        $fingerprints = [hash('sha256', 'first'), hash('sha256', 'second')];
        $manifest = collect($files)->map(fn (UploadedFile $file, int $index) => [
            'fingerprint' => $fingerprints[$index],
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'position' => $index,
        ])->all();

        $sessionResponse = $this->actingAs($user)
            ->postJson(route('admin.clients.gallery-uploads.store', $client), [
                'manifest' => $manifest,
                'notification_requested' => true,
            ])
            ->assertCreated();

        $session = GalleryUploadSession::where('uuid', $sessionResponse->json('uuid'))->firstOrFail();
        $batchUrl = route('admin.clients.gallery-uploads.batch', [$client, $session]);

        $this->actingAs($user)->post($batchUrl, [
            'files' => $files,
            'fingerprints' => $fingerprints,
        ], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('uploaded_files', 2);

        $this->assertDatabaseCount('client_images', 0);
        Mail::assertNothingSent();

        $this->actingAs($user)->post($batchUrl, [
            'files' => [
                UploadedFile::fake()->image('first.jpg', 1200, 800),
                UploadedFile::fake()->image('second.jpg', 800, 1200),
            ],
            'fingerprints' => $fingerprints,
        ], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('uploaded_files', 2);

        $this->assertDatabaseCount('gallery_upload_items', 2);

        $this->actingAs($user)
            ->postJson(route('admin.clients.gallery-uploads.complete', [$client, $session]))
            ->assertOk()
            ->assertJsonPath('status', 'completed');

        $this->assertDatabaseCount('client_images', 2);
        $this->assertDatabaseCount('gallery_upload_items', 0);
        Mail::assertSent(ClientWorkReady::class, 1);

        ClientImage::all()->each(function (ClientImage $image) {
            Storage::disk('public')->assertExists($image->image_path);
            Storage::disk('public')->assertExists($image->thumbnail_path);
            $this->assertNotNull($image->width);
            $this->assertNotNull($image->height);
        });

        $this->actingAs($user)
            ->postJson(route('admin.clients.gallery-uploads.complete', [$client, $session]))
            ->assertOk();
        Mail::assertSent(ClientWorkReady::class, 1);
    }

    public function test_reopening_the_same_manifest_returns_uploaded_fingerprints(): void
    {
        [$user, $client] = $this->userAndClient();
        $file = UploadedFile::fake()->image('resume.jpg', 800, 600);
        $fingerprint = hash('sha256', 'resume');
        $manifest = [[
            'fingerprint' => $fingerprint,
            'name' => 'resume.jpg',
            'size' => $file->getSize(),
            'position' => 0,
        ]];

        $created = $this->actingAs($user)->postJson(
            route('admin.clients.gallery-uploads.store', $client),
            ['manifest' => $manifest],
        )->assertCreated();
        $session = GalleryUploadSession::where('uuid', $created->json('uuid'))->firstOrFail();

        $this->actingAs($user)->post(
            route('admin.clients.gallery-uploads.batch', [$client, $session]),
            ['files' => [$file], 'fingerprints' => [$fingerprint]],
            ['Accept' => 'application/json'],
        )->assertOk();

        $this->actingAs($user)->postJson(
            route('admin.clients.gallery-uploads.store', $client),
            ['manifest' => $manifest],
        )->assertOk()->assertJsonPath('uploaded_fingerprints.0', $fingerprint);
    }

    public function test_public_gallery_uses_cursor_pagination(): void
    {
        [$user, $client] = $this->userAndClient();
        $client->update(['is_published' => true]);

        foreach (range(1, 30) as $order) {
            $client->images()->create([
                'image_path' => "clients/gallery-{$order}.jpg",
                'thumbnail_path' => "clients/gallery-{$order}-thumb.webp",
                'alt_text' => "Foto {$order}",
                'sort_order' => $order,
                'width' => 1200,
                'height' => 800,
            ]);
        }

        $this->get(route('clienti.show', $client))
            ->assertOk()
            ->assertSee('Foto 24')
            ->assertDontSee('Foto 25');

        $this->actingAs($user)->get(route('admin.clients.edit', $client))
            ->assertOk()
            ->assertSee('Foto 30');

        $firstPage = $this->getJson(route('clienti.gallery', $client))
            ->assertOk()
            ->assertJsonCount(24, 'items');

        $this->getJson(route('clienti.gallery', [
            'client' => $client,
            'cursor' => $firstPage->json('next_cursor'),
        ]))->assertOk()->assertJsonCount(6, 'items')->assertJsonPath('next_cursor', null);
    }

    public function test_prune_command_removes_expired_staging_files(): void
    {
        [$user, $client] = $this->userAndClient();
        $session = $client->galleryUploadSessions()->create([
            'uuid' => 'a4b18eb0-6c42-4a26-9cd6-99a4cf571234',
            'user_id' => $user->getKey(),
            'manifest_hash' => hash('sha256', 'expired'),
            'expected_files' => 1,
            'uploaded_files' => 1,
            'expected_bytes' => 4,
            'uploaded_bytes' => 4,
            'status' => 'active',
            'expires_at' => now()->subMinute(),
        ]);
        $session->items()->create([
            'fingerprint' => hash('sha256', 'expired-image'),
            'original_name' => 'expired.jpg',
            'byte_size' => 4,
            'position' => 0,
            'status' => 'uploaded',
            'staged_path' => 'gallery-uploads/expired/image.jpg',
            'staged_thumbnail_path' => 'gallery-uploads/expired/thumb.webp',
        ]);
        Storage::disk('local')->put('gallery-uploads/expired/image.jpg', 'test');
        Storage::disk('local')->put('gallery-uploads/expired/thumb.webp', 'test');

        $this->artisan('gallery-uploads:prune')->assertSuccessful();

        $this->assertDatabaseMissing('gallery_upload_sessions', ['id' => $session->getKey()]);
        Storage::disk('local')->assertMissing('gallery-uploads/expired/image.jpg');
        Storage::disk('local')->assertMissing('gallery-uploads/expired/thumb.webp');
    }

    private function userAndClient(): array
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Gallery Customer',
            'email' => 'gallery@example.com',
        ]);
        $client = Client::create([
            'customer_id' => $customer->getKey(),
            'name' => 'Gallery Work',
            'slug' => 'gallery-work',
            'description' => 'Gallery description',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => false,
            'is_portal_visible' => true,
        ]);

        return [$user, $client];
    }
}
