<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookHistory;
use ReadWorth\Domain\Services\BookService;
use ReadWorth\UI\Http\Resources\UpdateBookResource;
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

    public function update(UpdateBookResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);

        $this->deleteBookImage->delete($resource->getId());

        $workspace = new Workspace(id: $resource->getWorkspaceId(), name: $workspace->name);
        $book = new Book(
            id: $resource->getId(),
            category: $resource->getCategory(),
            status: $resource->getStatus(),
            title: $resource->getTitle(),
            description: $resource->getDescription(),
            imagePath: $resource->getImage() ? $this->storeBookImage->store($resource->getImage(), $resource->getWorkspaceId()) : null,
            url: $resource->getUrl(),
        );
        $auth = \Auth::user();
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);

        $action = $this->bookService->updateAction($resource->getId(), $resource->getStatus());
        $bookHistory = $action ? new BookHistory(action: $action) : null;

        $this->bookRepository->update($workspace, $book, $bookHistory, $user);
    }
}
