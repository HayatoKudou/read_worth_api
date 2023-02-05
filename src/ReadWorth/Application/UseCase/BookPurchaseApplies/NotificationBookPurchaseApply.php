<?php

namespace ReadWorth\Application\UseCase\BookPurchaseApplies;

use ReadWorth\Application\UseCase\Books\StoreBookImage;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookPurchaseApplyRepository;
use ReadWorth\UI\Http\Resources\NotificationBookPurchaseApplyResource;

class NotificationBookPurchaseApply
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

    public function notification(NotificationBookPurchaseApplyResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);

        \DB::transaction(function () use ($resource): void {
            $this->bookPurchaseApplyRepository->notification($resource->getBookId());

            if (!$resource->getSkip()) {
                $this->slackNotification->notification($resource->getTitle(), $resource->getMessage(), $resource->getWorkspaceId());
            }
        });
    }
}
