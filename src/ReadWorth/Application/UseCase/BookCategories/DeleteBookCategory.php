<?php

namespace ReadWorth\Application\UseCase\BookCategories;

use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\Domain\ValueObjects\BookCategoryId;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\UI\Http\Resources\DeleteBookCategoryResource;
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

    public function delete(DeleteBookCategoryResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);

        $bookCategoryId = new BookCategoryId();

        $workspace = new Workspace(id: $resource->getWorkspaceId(), name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId->getBookCategoryId(), name: $resource->getName());

        // ALLカテゴリは削除させない
        if ('ALL' === $bookCategory->getName()) {
            abort(422);
        }

        $this->bookCategoryRepository->delete($workspace, $bookCategory);
    }
}
