<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_uses_static_public_assets(): void
    {
        $this->assertFileExists(public_path('css/style.css'));
        $this->assertFileExists(public_path('css/admin.css'));
        $this->assertFileExists(public_path('js/app.js'));

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('css/style.css'), false)
            ->assertSee(asset('js/app.js'), false)
            ->assertDontSee('build/assets', false);

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee(asset('css/admin.css'), false)
            ->assertDontSee('build/assets', false);
    }

    public function test_homepage_shows_only_customers_with_a_logo(): void
    {
        Customer::create([
            'name' => 'Brand visibile',
            'email' => 'logo@example.com',
            'logo_path' => 'customer-logos/brand.png',
        ]);
        Customer::create([
            'name' => 'Cliente senza logo',
            'email' => 'senza-logo@example.com',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Brand visibile')
            ->assertSee('/storage/customer-logos/brand.png', false)
            ->assertDontSee('Cliente senza logo');
    }
}
