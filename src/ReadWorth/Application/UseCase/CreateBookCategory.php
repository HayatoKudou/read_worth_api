<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Domain\ValueObjects\BookCategoryId;
use ReadWorth\UI\Http\Requests\CreateBookCategoryRequest;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;

class CreateBookCategory
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookCategoryRepository $bookCategoryRepository,
    ) {
    }

    public function create(CreateBookCategoryRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $bookCategoryId = new BookCategoryId();

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId->getBookCategoryId(), name: $validated['name']);

        $this->bookCategoryRepository->store($workspace, $bookCategory);
    }
}
