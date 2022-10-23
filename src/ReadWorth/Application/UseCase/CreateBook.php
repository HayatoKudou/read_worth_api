<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\IBookRepository;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\IWorkspaceRepository;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\Domain\IBookCategoryRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\UI\Http\Requests\BookCategory\CreateRequest;

class CreateBook
{
    use AuthorizesRequests;

    public const STATUS_CAN_LEND = 1;
    public const STATUS_CAN_NOT_LEND = 2;
    public const STATUS_APPLYING = 3;

    public function __construct(
        private readonly IWorkspaceRepository $workspaceRepository,
        private readonly IBookCategoryRepository $bookCategoryRepository,
        private readonly IBookRepository $bookRepository,
    ) {
    }

    public function create(CreateRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $workspace = new Workspace(id: $workspaceId, name: $workspace->name);
        $bookCategory = new BookCategory(name: $validated['name']);

        $book = new Book(
            status: self::STATUS_CAN_LEND,
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['image'] ? $this->storeImage($validated['image'], $workspaceId) : null,
            url: $validated['url']
        );

        $this->bookRepository->store($workspace, $book, $bookCategory);
    }
}
