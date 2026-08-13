<?php

namespace Tests\Feature;

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
}
