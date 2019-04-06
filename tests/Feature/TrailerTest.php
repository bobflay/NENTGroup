<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrailerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGeneric()
    {
        $response = $this->get('/api/trailer?url=https://content.viaplay.se/pc-se/film/a-star-is-born-2018');
        $response->assertStatus(200);
        $this->assertEquals($response->getContent(),'https://www.youtube.com/watch?v=nSbzyEJ8X9E');
    }

    public function testForAnotherAPIResponse()
    {
        $response = $this->get('http://localhost:8000/api/trailer?url=https://content.viaplay.se/pc-se/film/avatar-2009');
        $response->assertStatus(200);
        $this->assertEquals($response->getContent(),'https://www.youtube.com/watch?v=5PSNL1qE6VY');
    }

}
