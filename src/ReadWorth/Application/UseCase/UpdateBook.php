<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\IBookRepository;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookHistory;
use ReadWorth\Domain\IWorkspaceRepository;
use ReadWorth\Domain\Services\BookService;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\UI\Http\Requests\UpdateBookRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UpdateBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
        private readonly IBookRepository $bookRepository,
        private readonly DeleteBookImage $deleteBookImage,
        private readonly StoreBookImage $storeBookImage,
        private readonly BookService $bookService,
    ) {
    }

    public function update(UpdateBookRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $this->deleteBookImage->delete($validated['id']);

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(name: $validated['category']);
        $book = new Book(
            id: $validated['id'],
            status: $validated['status'],
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['image'] ? $this->storeBookImage->store($validated['image'], $workspaceId) : null,
            url: $validated['url']
        );

        $action = $this->bookService->updateAction($validated['id'], $validated['status']);
        $bookHistory = $action ? new BookHistory(action: $action) : null;

        $this->bookRepository->update($workspace, $book, $bookCategory, $bookHistory);
    }
}
