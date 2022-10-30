<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\IWorkspaceRepository;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\Domain\IBookCategoryRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\UI\Http\Requests\CreateBookCategoryRequest;

class CreateBookCategory
{
    use AuthorizesRequests;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
        private readonly IBookCategoryRepository $bookCategoryRepository,
    ) {
    }

    public function create(CreateBookCategoryRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $bookCategoryId = time() . \Auth::id();

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId, name: $validated['name']);

        $this->bookCategoryRepository->store($workspace, $bookCategory);
    }
}
