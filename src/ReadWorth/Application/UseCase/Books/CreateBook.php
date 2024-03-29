<?php

namespace ReadWorth\Application\UseCase\Books;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\UI\Http\Resources\CreateBookResource;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class CreateBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookRepository $bookRepository,
        private readonly StoreBookImage $storeBookImage,
    ) {
    }

    public function create(CreateBookResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);

        $bookId = new BookId();

        $workspace = new Workspace(id: $resource->getWorkspaceId(), name: $workspace->name);
        $book = new Book(
            id: $bookId->getBookId(),
            category: $resource->getCategory(),
            status: BookStatus::STATUS_CAN_LEND,
            title: $resource->getTitle(),
            description: $resource->getDescription(),
            imagePath: $resource->getImage() ? $this->storeBookImage->store($resource->getImage(), $resource->getWorkspaceId()) : null,
            url: $resource->getUrl()
        );
        $auth = \Auth::user();
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);

        $this->bookRepository->store($workspace, $book, $user);
    }
}
