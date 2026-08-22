<?php

namespace Tests\Feature;

use App\Models\ClientLogo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_logo_admin_requires_login(): void
    {
        $this->get(route('admin.client-logos.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_upload_multiple_client_logos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.client-logos.store'), [
                'logos' => [
                    UploadedFile::fake()->image('brand-uno.png', 400, 200),
                    UploadedFile::fake()->image('brand-due.jpg', 400, 200),
                ],
            ])
            ->assertRedirect(route('admin.client-logos.index'));

        $this->assertDatabaseCount('client_logos', 2);
        ClientLogo::all()->each(fn (ClientLogo $logo) => Storage::disk('public')->assertExists($logo->image_path));
    }

    public function test_admin_page_lists_uploaded_logos_and_mass_upload_controls(): void
    {
        $user = User::factory()->create();
        $clientLogo = ClientLogo::create([
            'name' => 'Brand in homepage',
            'image_path' => 'client-logos/brand.png',
            'original_name' => 'brand.png',
            'sort_order' => 3,
        ]);

        $this->actingAs($user)
            ->get(route('admin.client-logos.index'))
            ->assertOk()
            ->assertSee('Caricamento massivo')
            ->assertSee('data-client-logo-upload', false)
            ->assertSee('multiple', false)
            ->assertSee('Brand in homepage')
            ->assertSee(route('admin.client-logos.update', $clientLogo), false);
    }

    public function test_admin_can_replace_and_edit_a_client_logo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Storage::disk('public')->put('client-logos/old.png', 'old-logo');
        $clientLogo = ClientLogo::create([
            'name' => 'Vecchio nome',
            'image_path' => 'client-logos/old.png',
            'original_name' => 'old.png',
            'sort_order' => 1,
        ]);

        $this->actingAs($user)
            ->put(route('admin.client-logos.update', $clientLogo), [
                'name' => 'Nuovo nome',
                'sort_order' => 8,
                'logo' => UploadedFile::fake()->image('new-logo.webp', 400, 200),
            ])
            ->assertRedirect(route('admin.client-logos.index'));

        $clientLogo->refresh();
        $this->assertSame('Nuovo nome', $clientLogo->name);
        $this->assertSame(8, $clientLogo->sort_order);
        Storage::disk('public')->assertMissing('client-logos/old.png');
        Storage::disk('public')->assertExists($clientLogo->image_path);
    }

    public function test_admin_can_delete_a_client_logo_and_its_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Storage::disk('public')->put('client-logos/to-delete.png', 'logo');
        $clientLogo = ClientLogo::create([
            'name' => 'Da eliminare',
            'image_path' => 'client-logos/to-delete.png',
            'sort_order' => 1,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.client-logos.destroy', $clientLogo))
            ->assertRedirect(route('admin.client-logos.index'));

        $this->assertModelMissing($clientLogo);
        Storage::disk('public')->assertMissing('client-logos/to-delete.png');
    }
}
