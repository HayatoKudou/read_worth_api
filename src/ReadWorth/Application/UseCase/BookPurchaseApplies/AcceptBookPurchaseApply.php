<?php

namespace ReadWorth\Application\UseCase\BookPurchaseApplies;

use ReadWorth\Application\UseCase\Books\StoreBookImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookPurchaseApplyRepository;

class AcceptBookPurchaseApply
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookPurchaseApplyRepository $bookPurchaseApplyRepository,
        private readonly StoreBookImage $storeBookImage,
        private readonly SlackNotification $slackNotification,
    ) {
    }

    public function accept(string $workspaceId, string $bookId): void
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);

        $this->bookPurchaseApplyRepository->accept($bookId, \Auth::id());
    }
}
