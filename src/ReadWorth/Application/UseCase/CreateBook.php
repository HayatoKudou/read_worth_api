<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookCategory;
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

    public function create(CreateBookResource $createBookResource): void
    {
        $workspace = $this->workspaceRepository->findById($createBookResource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);

        // TODO: どうにかしたい
        $bookId = time() . \Auth::id();
        $bookCategoryId = time() . \Auth::id();

        $workspace = new Workspace(id: $createBookResource->getWorkspaceId(), name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId, name: $createBookResource->getCategory());
        $book = new Book(
            id: $bookId,
            status: BookStatus::STATUS_CAN_LEND,
            title: $createBookResource->getTitle(),
            description: $createBookResource->getDescription(),
            imagePath: $createBookResource->getImage() ? $this->storeBookImage->store($createBookResource->getImage(), $createBookResource->getWorkspaceId()) : null,
            url: $createBookResource->getUrl()
        );

        $this->bookRepository->store($workspace, $book, $bookCategory);
    }
}
