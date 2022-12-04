<?php

namespace ReadWorth\Application\UseCase\BookPurchaseApplies;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\BookPurchaseApply;
use ReadWorth\Application\UseCase\Books\StoreBookImage;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Domain\ValueObjects\BookPurchaseApplySteps;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookPurchaseApplyRepository;

class RefuseBookPurchaseApply
{
    use AuthorizesRequests;

    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookPurchaseApplyRepository $bookPurchaseApplyRepository,
        private readonly StoreBookImage $storeBookImage,
        private readonly SlackNotification $slackNotification,
    ) {
    }

    public function refuse(string $workspaceId, string $bookId): void
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);

        $bookRepo = $this->bookRepository->findById($bookId);
        $auth = \Auth::user();

        $book = new Book(
            id: $bookRepo->id,
            category: $bookRepo->category,
            status: $bookRepo->status,
            title: $bookRepo->title,
            description: $bookRepo->description,
            imagePath: $bookRepo->image_path,
            url: $bookRepo->url
        );
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);
        $bookPurchaseApply = new BookPurchaseApply(
            reason: $bookRepo->purchaseApply->reason,
            price: $bookRepo->purchaseApply->price,
            step: BookPurchaseApplySteps::REFUSED,
            location: $bookRepo->purchaseApply->location
        );

        $this->bookPurchaseApplyRepository->done($book, $user, $bookPurchaseApply);
    }
}
