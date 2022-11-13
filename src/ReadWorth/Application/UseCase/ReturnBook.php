<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\UI\Http\Resources\ReturnBookResource;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class ReturnBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookRepository $bookRepository,
        private readonly StoreBookImage $storeBookImage,
    ) {
    }

    public function return(ReturnBookResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);

        $auth = \Auth::user();
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);
        $this->bookRepository->return(new BookId($resource->getBookId()), $user);
    }
}
