<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    public function testLoadHome()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
