<?php

namespace ReadWorth\Application\UseCase\BookHistories;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\QueryService\BookHistoriesQueryService;

class GetBookHistories
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookHistoriesQueryService $bookHistoriesQueryService
    ) {
    }

    public function get(int $workspaceId, int $bookId): array
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        return $this->bookHistoriesQueryService->getBookHistories($bookId);
    }
}
