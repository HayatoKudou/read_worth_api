<?php

namespace Tests\Unit\Api;

use App\Models\Plan;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Plan::factory()->create(['name' => "premier"]);
    }

    /** @test */
    public function バリデーションで弾かれること()
    {
        $response = $this->json('POST', '/api/signUp', [
            'name' => '検証 太郎',
        ]);
        $response->assertStatus(422);
    }
}
