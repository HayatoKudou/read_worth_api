<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookHistory;
use ReadWorth\Domain\Services\BookService;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\UI\Http\Requests\UpdateBookRequest;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class UpdateBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookRepository $bookRepository,
        private readonly StoreBookImage $storeBookImage,
        private readonly DeleteBookImage $deleteBookImage,
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
        $bookCategory = new BookCategory(id: $validated['id'], name: $validated['category']);
        $book = new Book(
            id: $validated['id'],
            status: $validated['status'],
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['image'] ? $this->storeBookImage->store($validated['image'], $workspaceId) : null,
            url: $validated['url']
        );
        $auth = \Auth::user();
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);

        $action = $this->bookService->updateAction($validated['id'], $validated['status']);
        $bookHistory = $action ? new BookHistory(action: $action) : null;

        $this->bookRepository->update($workspace, $book, $bookCategory, $bookHistory, $user);
    }
}
