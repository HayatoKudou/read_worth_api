<?php

namespace Tests\Feature\E2E;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::factory()->create(['name' => 'premier']);
        $workspace = Workspace::factory()->create([
            'name' => '株式会社かぶかぶ',
            'plan_id' => $plan->id,
        ]);
        $user = User::factory()->create([
            'workspace_id' => $workspace->id,
            'email' => 'aaabbbccc@test.com',
            'password' => Hash::make('pass'),
        ]);
        Role::factory()->create([
            'user_id' => $user->id,
            'is_account_manager' => 1,
            'is_book_manager' => 1,
            'is_workspace_manager' => 1,
        ]);
    }

    /** @test */
    public function サインアップができること(): void
    {
        $response = $this->json('POST', '/api/signUp', [
            'name' => '検証 太郎',
            'email' => 'aaa@test.com',
            'password' => 'password',
            'client_name' => '株式会社検証',
            'plan' => 'premier',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function サインインができること(): void
    {
        $response = $this->json('POST', '/api/signIn', [
            'email' => 'aaabbbccc@test.com',
            'password' => 'pass',
        ]);
        $response->assertOk();
    }
}
