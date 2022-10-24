<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\IWorkspaceRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\QueryService\BooksQueryService;

class FetchBooks
{
    use AuthorizesRequests;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
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
