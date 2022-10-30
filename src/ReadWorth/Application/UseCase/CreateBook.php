<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\IBookRepository;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\IWorkspaceRepository;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\Domain\IBookCategoryRepository;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\UI\Http\Requests\CreateBookRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CreateBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
        private readonly IBookCategoryRepository $bookCategoryRepository,
        private readonly IBookRepository $bookRepository,
        private readonly StoreBookImage $storeBookImage,
    ) {
    }

    public function create(CreateBookRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        // TODO: どうにかしたい
        $bookId = time().\Auth::id();
        $bookCategoryId = time().\Auth::id();

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(id: $bookCategoryId, name: $validated['category']);
        $book = new Book(
            id: $bookId,
            status: BookStatus::STATUS_CAN_LEND,
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['image'] ? $this->storeBookImage->store($validated['image'], $workspaceId) : null,
            url: $validated['url']
        );

        $this->bookRepository->store($workspace, $book, $bookCategory);
    }
}
