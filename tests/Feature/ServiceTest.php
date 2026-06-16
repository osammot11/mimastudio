<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServiceTest extends TestCase
{
    public function test_services_page_can_be_filtered_by_category(): void
    {
        $this->get('/servizi?categoria=ritratti')
            ->assertOk()
            ->assertSee('Ritratti per persone')
            ->assertDontSee('Immagini per brand che vogliono comunicare');
    }
}
