<?php

namespace Tests\Feature\E2E;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Belonging;
use App\Models\Workspace;

class BookCategoryControllerTest extends TestCase
{
    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->create();
        $user = User::factory()->create([
            'api_token' => 'aaaaaaa',
        ]);
        $role = Role::factory()->create();
        Belonging::factory()->create([
            'user_id' => $user->id,
            'workspace_id' => $this->workspace->id,
            'role_id' => $role->id,
        ]);
    }

    /** @test */
    public function 書籍カテゴリの登録ができること(): void
    {
        $response = $this->json('POST', '/api/' . $this->workspace->id . '/bookCategory', [
            'name' => 'マネジメント',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(201);
    }

    /** @test */
    public function 書籍カテゴリの削除ができること(): void
    {
        $response = $this->json('DELETE', '/api/' . $this->workspace->id . '/bookCategory', [
            'name' => 'マネジメント',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(200);
    }
}
