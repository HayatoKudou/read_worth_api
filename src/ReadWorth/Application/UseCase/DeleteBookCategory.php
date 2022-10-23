<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\UI\Http\Requests\BookCategory\DeleteRequest;
use ReadWorth\Infrastructure\Repository\IWorkspaceRepository;
use ReadWorth\Infrastructure\Repository\IBookCategoryRepository;

class DeleteBookCategory
{
    use AuthorizesRequests;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
        private readonly IBookCategoryRepository $bookCategoryRepository,
    ) {
    }

    public function delete(DeleteRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(name: $validated['name']);

        // ALLカテゴリは削除させない
        if ('ALL' === $bookCategory->getName()) {
            abort(422);
        }

        $this->bookCategoryRepository->delete($workspace, $bookCategory);
    }
}
