<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'site.access_enabled' => true,
            'site.access_code' => '1234',
        ]);
    }

    public function test_public_pages_require_the_access_code(): void
    {
        $this->get('/')
            ->assertRedirect(route('site-access.show'));

        $this->get('/portfolio')
            ->assertRedirect(route('site-access.show'));

        $this->get(route('admin.login'))
            ->assertOk();
    }

    public function test_correct_code_unlocks_the_public_site_for_the_session(): void
    {
        $this->from('/')
            ->post(route('site-access.authenticate'), ['code' => '1234'])
            ->assertRedirect('/');

        $this->get('/')
            ->assertOk();
    }

    public function test_wrong_code_does_not_unlock_the_site(): void
    {
        $this->post(route('site-access.authenticate'), ['code' => '0000'])
            ->assertSessionHasErrors('code');

        $this->get('/')
            ->assertRedirect(route('site-access.show'));
    }
}
