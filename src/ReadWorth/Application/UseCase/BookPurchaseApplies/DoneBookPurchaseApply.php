<?php

namespace ReadWorth\Application\UseCase\BookPurchaseApplies;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Domain\Entities\BookPurchaseApply;
use ReadWorth\Application\UseCase\Books\StoreBookImage;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Domain\ValueObjects\BookPurchaseApplySteps;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\UI\Http\Resources\DoneBookPurchaseApplyResource;
use ReadWorth\Infrastructure\Repository\BookPurchaseApplyRepository;

class DoneBookPurchaseApply
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

    public function done(DoneBookPurchaseApplyResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);

        $bookRepo = $this->bookRepository->findById($resource->getBookId());
        $auth = \Auth::user();

        $book = new Book(
            id: $bookRepo->id,
            category: $bookRepo->category,
            status: BookStatus::STATUS_CAN_LEND,
            title: $bookRepo->title,
            description: $bookRepo->description,
            imagePath: $bookRepo->image_path,
            url: $bookRepo->url
        );
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);
        $bookPurchaseApply = new BookPurchaseApply(
            reason: $bookRepo->purchaseApply->reason,
            price: $bookRepo->purchaseApply->price,
            step: BookPurchaseApplySteps::NEED_NOTIFICATION,
            location: $resource->getLocation()
        );

        $this->bookPurchaseApplyRepository->done($book, $user, $bookPurchaseApply);
    }
}
