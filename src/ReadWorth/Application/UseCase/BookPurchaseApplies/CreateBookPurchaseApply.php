<?php

namespace ReadWorth\Application\UseCase\BookPurchaseApplies;

use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Domain\Entities\BookPurchaseApply;
use ReadWorth\Application\UseCase\Books\StoreBookImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Domain\ValueObjects\BookPurchaseApplySteps;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\UI\Http\Resources\CreateBookPurchaseApplyResource;
use ReadWorth\Infrastructure\Repository\BookPurchaseApplyRepository;

class CreateBookPurchaseApply
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookPurchaseApplyRepository $bookPurchaseApplyRepository,
        private readonly StoreBookImage $storeBookImage,
        private readonly SlackNotification $slackNotification,
    ) {
    }

    public function create(CreateBookPurchaseApplyResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);

        $auth = \Auth::user();
        $bookId = new BookId();

        $workspace = new Workspace(id: $resource->getWorkspaceId(), name: $workspace->name);
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);
        $book = new Book(
            id: $bookId->getBookId(),
            category: $resource->getCategory(),
            status: BookStatus::STATUS_APPLYING,
            title: $resource->getTitle(),
            description: $resource->getDescription(),
            imagePath: $resource->getImage() ? $this->storeBookImage->store($resource->getImage(), $resource->getWorkspaceId()) : null,
            url: $resource->getUrl()
        );
        $bookPurchaseApply = new BookPurchaseApply(
            reason: $resource->getReason(),
            price: $resource->getPrice(),
            step: BookPurchaseApplySteps::NEED_ACCEPT,
            location: null
        );

        $this->bookPurchaseApplyRepository->store($workspace, $user, $book, $bookPurchaseApply);

        $title = '書籍購入申請のお知らせ';
        $message = '【タイトル】' . $resource->getTitle() . "\n【申請者】" . $user->getName();
        $this->slackNotification->notification($title, $message, $resource->getWorkspaceId());
    }
}
