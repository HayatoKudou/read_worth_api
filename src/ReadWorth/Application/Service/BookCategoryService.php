<?php

namespace ReadWorth\Application\Service;

use ReadWorth\Domain\BookCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\IWorkspaceRepository;
use ReadWorth\Infrastructure\Repository\IBookCategoryRepository;

class BookCategoryService
{
    use AuthorizesRequests;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
        private readonly IBookCategoryRepository $bookCategoryRepository,
    ) {
    }

    public function create(BookCategory $bookCategory): void
    {
        $workspace = $this->workspaceRepository->findById($bookCategory->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->bookCategoryRepository->store($bookCategory);
    }
}
