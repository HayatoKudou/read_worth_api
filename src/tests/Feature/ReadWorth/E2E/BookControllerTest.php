<?php

namespace Tests\Feature\ReadWorth\E2E;

use Tests\TestCase;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookControllerTest extends TestCase
{
    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->create();
        $user = User::factory()->create(['api_token' => 'aaaaaaa']);
        $role = Role::factory()->create();
        BookCategory::factory()->create([
           'workspace_id' => $this->workspace->id,
           'name' => 'マネジメント',
        ]);
        Belonging::factory()->create([
           'user_id' => $user->id,
           'workspace_id' => $this->workspace->id,
           'role_id' => $role->id,
        ]);
    }

    /** @test */
    public function 書籍登録できること(): void
    {
        $response = $this->json('POST', '/api/' . $this->workspace->id . '/book', [
           'category' => 'マネジメント',
           'title' => 'すごい本',
           'description' => 'すごい本だよ',
           'image' => '',
           'url' => '',
        ], [
           'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(201);
    }

    /** @test */
    public function 書籍更新できること(): void
    {
        $book = Book::factory()->create(['workspace_id' => $this->workspace->id, 'title' => 'やばばば']);
        assert($book instanceof Book);

        $response = $this->json('PUT', '/api/' . $this->workspace->id . '/book', [
            'id' => $book->id,
            'title' => 'やばばば ver2',
            'description' => 'やばばばの2弾',
            'category' => 'マネジメント',
            'status' => BookStatus::STATUS_CAN_LEND,
            'image' => '',
            'url' => '',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(200);
    }
}
