<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Domain\ValueObjects\BookCategoryId;
use ReadWorth\UI\Http\Requests\DeleteBookCategoryRequest;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;

class DeleteBookCategory
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookCategoryRepository $bookCategoryRepository,
    ) {
    }

    public function delete(DeleteBookCategoryRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $bookCategoryId = new BookCategoryId();

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId->getBookCategoryId(), name: $validated['name']);

        // ALLカテゴリは削除させない
        if ('ALL' === $bookCategory->getName()) {
            abort(422);
        }

        $this->bookCategoryRepository->delete($workspace, $bookCategory);
    }
}
