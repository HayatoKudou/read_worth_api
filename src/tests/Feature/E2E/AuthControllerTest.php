<?php

namespace Tests\Feature\E2E;

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
    public function サインアップができること()
    {
        $response = $this->json('POST', '/api/signUp', [
            'name' => '検証 太郎',
            'email' => 'test@test.test',
            'password' => 'password',
            'client_name' => '検証会社',
            'plan' => 'premier',
        ]);
        \Log::debug($response->json());
        $response->assertOk();
    }
}
