<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Domain\ValueObjects\BookCategoryId;
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

    public function create(CreateBookResource $createBookResource): void
    {
        $workspace = $this->workspaceRepository->findById($createBookResource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);

        $bookId = new BookId();
        $bookCategoryId = new BookCategoryId();

        $workspace = new Workspace(id: $createBookResource->getWorkspaceId(), name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId->getBookCategoryId(), name: $createBookResource->getCategory());
        $book = new Book(
            id: $bookId->getBookId(),
            status: BookStatus::STATUS_CAN_LEND,
            title: $createBookResource->getTitle(),
            description: $createBookResource->getDescription(),
            imagePath: $createBookResource->getImage() ? $this->storeBookImage->store($createBookResource->getImage(), $createBookResource->getWorkspaceId()) : null,
            url: $createBookResource->getUrl()
        );
        $auth = \Auth::user();
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);

        $this->bookRepository->store($workspace, $book, $bookCategory, $user);
    }
}
