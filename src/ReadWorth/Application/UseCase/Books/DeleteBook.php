<?php

namespace ReadWorth\Application\UseCase\Books;

use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\UI\Http\Resources\DeleteBookResource;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class DeleteBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookRepository $bookRepository,
    ) {
    }

    public function delete(DeleteBookResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);

        $bookIds = collect($resource->getBookIds())->map(fn ($bookId) => new BookId($bookId));
        $this->bookRepository->delete($bookIds);
    }
}
