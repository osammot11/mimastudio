<?php

namespace Tests\Feature;

use App\Models\PortfolioProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_index_shows_only_published_projects(): void
    {
        PortfolioProject::create([
            'title' => 'Published Project',
            'slug' => 'published-project',
            'description' => 'Visible description',
            'cover_image' => 'images/portfolio-1.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        PortfolioProject::create([
            'title' => 'Hidden Project',
            'slug' => 'hidden-project',
            'description' => 'Hidden description',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('Published Project')
            ->assertDontSee('Hidden Project');
    }

    public function test_published_project_detail_is_visible_and_hidden_project_is_not(): void
    {
        PortfolioProject::create([
            'title' => 'Published Project',
            'slug' => 'published-project',
            'description' => 'Visible description',
            'cover_image' => 'images/portfolio-1.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        PortfolioProject::create([
            'title' => 'Hidden Project',
            'slug' => 'hidden-project',
            'description' => 'Hidden description',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get('/portfolio/published-project')
            ->assertOk()
            ->assertSee('Published Project');

        $this->get('/portfolio/hidden-project')
            ->assertNotFound();
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_create_project(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/admin/portfolio');

        $this->post('/admin/portfolio', [
            'title' => 'Admin Project',
            'description' => 'Created from admin',
            'body' => 'Long body',
            'category' => 'Test',
            'sort_order' => 1,
            'is_published' => '1',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect();

        $this->assertDatabaseHas('portfolio_projects', [
            'title' => 'Admin Project',
            'slug' => 'admin-project',
            'is_published' => true,
        ]);
    }
}
