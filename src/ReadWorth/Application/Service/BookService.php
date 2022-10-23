<?php

namespace ReadWorth\Application\Service;

use ReadWorth\Domain;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Book\CreateRequest;
use App\Http\Requests\Book\UpdateRequest;
use ReadWorth\Infrastructure\Repository\IBookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Infrastructure\Repository\IWorkspaceRepository;
use ReadWorth\Infrastructure\Repository\IBookCategoryRepository;

class BookService
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

        $workspaceDomain = new Domain\Workspace(name: $workspace->name);
        $bookCategoryDomain = new Domain\BookCategory(name: $validated['category']);

        $bookDomain = new Domain\Book(
            status: self::STATUS_CAN_LEND,
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['image'] ? $this->storeImage($validated['image'], $workspaceId) : null,
            url: $validated['url']
        );

        $this->bookRepository->store($workspaceDomain, $bookDomain, $bookCategoryDomain);
    }

    public function update(UpdateRequest $request): void
    {
        $workspaceId = $request->route('workspaceId');
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $workspaceDomain = new Domain\Workspace(name: $workspace->name);
        $bookCategoryDomain = new Domain\BookCategory(name: $validated['category']);
        $bookDomain = new Domain\Book(
            status: $validated['status'],
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['imagePath'] ? $this->storeImage($validated['image'], $workspaceId) : null,
            url: $validated['url']
        );

        $this->bookRepository->update($workspaceDomain, $bookDomain, $bookCategoryDomain);
    }

    public static function storeImage(string $imageBinary, string $workspaceId): string
    {
        $user = Auth::user();
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
        $imagePath = $workspaceId . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        Storage::put($imagePath, base64_decode($file_data, true));
        return $imagePath;
    }
}
