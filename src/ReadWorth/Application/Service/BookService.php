<?php

namespace ReadWorth\Application\Service;

use ReadWorth\Domain;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Book\CreateRequest;
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
        $workspace = $this->workspaceRepository->findById($request->route('workspaceId'));
        $this->authorize('affiliation', $workspace);
        $this->authorize('isBookManager', $workspace);
        $validated = $request->validated();

        $bookCategoryDomain = new Domain\BookCategory(
            workspaceId: $request->route('workspaceId'),
            name: $validated['category']
        );

        $bookCategory = $this->bookCategoryRepository->findByWorkspaceIdAndName($bookCategoryDomain->getWorkspaceId(), $bookCategoryDomain->getName());
        $bookDomain = new Domain\Book(
            workspaceId: $request->route('workspaceId'),
            bookCategoryId: $bookCategory->id,
            status: self::STATUS_CAN_LEND,
            title: $validated['title'],
            description: $validated['description'],
            imagePath: $validated['image'] ? $this->storeImage($validated['image'], $bookCategoryDomain->getWorkspaceId()) : null,
            url: $validated['url']
        );

        $this->bookRepository->store($bookDomain, $bookCategoryDomain);
    }

//    public function delete(BookCategory $bookCategory): void
//    {
//        $workspace = $this->workspaceRepository->findById($bookCategory->getWorkspaceId());
//        $this->authorize('affiliation', $workspace);
//        $this->authorize('isBookManager', $workspace);
//
//        // ALLカテゴリは削除させない
//        if ('ALL' === $bookCategory->getName()) {
//            abort(422);
//        }
//
//        $this->bookRepository->delete($bookCategory);
//    }

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
