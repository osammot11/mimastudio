<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_index_shows_only_published_clients(): void
    {
        Client::create([
            'name' => 'Visible Client',
            'slug' => 'visible-client',
            'description' => 'Visible description',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Client::create([
            'name' => 'Hidden Client',
            'slug' => 'hidden-client',
            'description' => 'Hidden description',
            'photo_image' => 'images/portfolio-3.jpeg',
            'cover_image' => 'images/portfolio-4.jpeg',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get('/clienti')
            ->assertOk()
            ->assertSee('Visible Client')
            ->assertDontSee('Hidden Client');
    }

    public function test_published_client_detail_is_visible_and_hidden_client_is_not(): void
    {
        Client::create([
            'name' => 'Visible Client',
            'slug' => 'visible-client',
            'description' => 'Visible description',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Client::create([
            'name' => 'Hidden Client',
            'slug' => 'hidden-client',
            'description' => 'Hidden description',
            'photo_image' => 'images/portfolio-3.jpeg',
            'cover_image' => 'images/portfolio-4.jpeg',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get('/clienti/visible-client')
            ->assertOk()
            ->assertSee('Visible Client');

        $this->get('/clienti/hidden-client')
            ->assertNotFound();
    }

    public function test_admin_can_create_client(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $this->actingAs($user)->post('/admin/clients', [
            'name' => 'Admin Client',
            'description' => 'Created from admin',
            'client_date' => '2026-06-10',
            'sort_order' => 1,
            'is_published' => '1',
            'photo_image' => UploadedFile::fake()->image('photo.jpg'),
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'name' => 'Admin Client',
            'slug' => 'admin-client',
            'is_published' => true,
        ]);
    }
}
