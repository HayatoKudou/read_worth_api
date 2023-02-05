<?php

namespace ReadWorth\Application\UseCase\BookRentalApplies;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Infrastructure\EloquentModel;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Application\UseCase\Books\StoreBookImage;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookPurchaseApplyRepository;

class BookRentalApply
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

    public function rentalApply(string $workspaceId, string $bookId, string $reason, string $expectedReturnDate)
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);
        $this->authorize('affiliation', $workspace);

        $book = EloquentModel\Book::find($bookId);
        $user = EloquentModel\User::find(Auth::id());

        $title = '書籍貸出のお知らせ';
        $message = '【申請者】' . $user->name . "\n【貸出理由】" . $reason . "\n【返却予定日】" . $expectedReturnDate;
        $this->slackNotification->notification($title, $message, $workspace->id, $book->image_path);

        return DB::transaction(function () use ($book, $user, $workspaceId, $bookId, $reason, $expectedReturnDate): JsonResponse {
            $book->update(['status' => BookStatus::STATUS_CAN_NOT_LEND]);
            EloquentModel\BookRentalApply::create([
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,
                'book_id' => $bookId,
                'reason' => $reason,
                'rental_date' => Carbon::now(),
                'expected_return_date' => Carbon::parse($expectedReturnDate),
            ]);
            EloquentModel\BookHistory::create([
                'book_id' => $book->id,
                'user_id' => Auth::id(),
                'action' => 'lend book',
            ]);
            return response()->json([], 201);
        });
    }
}
