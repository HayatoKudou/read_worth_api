<?php

namespace ReadWorth\Application\UseCase;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\QueryService\BooksQueryService;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class FetchBooks
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BooksQueryService $booksQueryService
    ) {
    }

    public function fetch(string $workspaceId): array
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        return $this->booksQueryService->fetchBooks($workspaceId);
    }
}
