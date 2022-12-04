<?php

namespace ReadWorth\UI\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Slack\SlackApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ReadWorth\Infrastructure\EloquentModel\Book;
use App\Http\Requests\BookPurchaseApply\DoneRequest;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\SlackCredential;
use App\Http\Requests\BookPurchaseApply\NotificationRequest;
use ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply;
use ReadWorth\UI\Http\Requests\CreateBookPurchaseApplyRequest;
use ReadWorth\UI\Http\Resources\DoneBookPurchaseApplyResource;
use ReadWorth\UI\Http\Resources\CreateBookPurchaseApplyResource;
use ReadWorth\Application\UseCase\BookPurchaseApplies\DoneBookPurchaseApply;
use ReadWorth\Application\UseCase\BookPurchaseApplies\AcceptBookPurchaseApply;
use ReadWorth\Application\UseCase\BookPurchaseApplies\CreateBookPurchaseApply;
use ReadWorth\Application\UseCase\BookPurchaseApplies\RefuseBookPurchaseApply;

class BookPurchaseApplyController extends Controller
{
    public function __construct(
        private readonly CreateBookPurchaseApply $createBookPurchaseApply,
        private readonly AcceptBookPurchaseApply $acceptBookPurchaseApply,
        private readonly DoneBookPurchaseApply $doneBookPurchaseApply,
        private readonly RefuseBookPurchaseApply $refuseBookPurchaseApply,
    ) {
    }

    public function list(string $workspaceId): JsonResponse
    {
        $workspace = Workspace::find($workspaceId);
        $this->authorize('affiliation', $workspace);
        assert($workspace instanceof Workspace);
        $bookPurchaseApplies = BookPurchaseApply::where('workspace_id', $workspaceId)->get();
        return response()->json([
            'slackCredentialExists' => $workspace->slackCredential()->whereNotNull('access_token')->exists(),
            'bookPurchaseApplies' => $bookPurchaseApplies->map(fn (BookPurchaseApply $bookPurchaseApply) => [
                'reason' => $bookPurchaseApply->reason,
                'price' => $bookPurchaseApply->price,
                'step' => $bookPurchaseApply->step,
                'location' => $bookPurchaseApply->location,
                'createdAt' => Carbon::parse($bookPurchaseApply->created_at)->format('Y/m/d'),
                'user' => [
                    'id' => $bookPurchaseApply->user->id,
                    'name' => $bookPurchaseApply->user->name,
                    'email' => $bookPurchaseApply->user->email,
                ],
                'book' => [
                    'id' => $bookPurchaseApply->book->id,
                    'status' => $bookPurchaseApply->book->status,
                    'category' => $bookPurchaseApply->book->category->name,
                    'title' => $bookPurchaseApply->book->title,
                    'description' => $bookPurchaseApply->book->description,
                    'image' => $bookPurchaseApply->book->image_path ? base64_encode(Storage::get($bookPurchaseApply->book->image_path)) : null,
                    'url' => $bookPurchaseApply->book->url,
                    'createdAt' => Carbon::parse($bookPurchaseApply->book->created_at)->format('Y年m月d日'),
                ],
            ]),
        ]);
    }

    public function create(CreateBookPurchaseApplyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->createBookPurchaseApply->create(new CreateBookPurchaseApplyResource(
            workspaceId: $request->route('workspaceId'),
            category: $validated['category'],
            title: $validated['title'],
            reason: $validated['reason'],
            price: $validated['price'],
            description: $validated['description'],
            image: $validated['image'],
            url: $validated['url'],
        ));
        return response()->json([], 201);
    }

    public function accept(string $workspaceId, string $bookId): JsonResponse
    {
        $this->acceptBookPurchaseApply->accept($workspaceId, $bookId);
        return response()->json([]);
    }

    public function done(string $workspaceId, string $bookId, DoneRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->doneBookPurchaseApply->done(new DoneBookPurchaseApplyResource(
            workspaceId: $workspaceId,
            bookId: $bookId,
            location: $validated['location']
        ));
        return response()->json([]);
    }

    public function refuse(string $workspaceId, string $bookId): JsonResponse
    {
        $this->refuseBookPurchaseApply->refuse($workspaceId, $bookId);
        return response()->json([]);
    }

    public function init(string $workspaceId, string $bookId): JsonResponse
    {
        $workspace = Workspace::find($workspaceId);
        $this->authorize('affiliation', $workspace);
        DB::transaction(function () use ($bookId): void {
            Book::find($bookId)->purchaseApply->update([
                'step' => BookPurchaseApply::NEED_ACCEPT,
            ]);
            BookHistory::create([
                'book_id' => $bookId,
                'user_id' => Auth::id(),
                'action' => 'purchase init',
            ]);
        });
        return response()->json([]);
    }

    public function notification(string $workspaceId, string $bookId, NotificationRequest $request): JsonResponse
    {
        $workspace = Workspace::find($workspaceId);
        $this->authorize('affiliation', $workspace);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($workspaceId, $bookId, $validated) {
                $book = Book::find($bookId);
                $book->purchaseApply->delete();

                if ($validated['skip']) {
                    return response()->json([]);
                }

                // 通知が失敗したらロールバック
                $slackCredential = SlackCredential::where('workspace_id', $workspaceId)->first();

                if (!$slackCredential) {
                    return response()->json(['errors' => [
                        'slack' => 'Slack連携がされていません。',
                    ]], 500);
                }
                $slackClient = new SlackApiClient(new Client(), $slackCredential->access_token);
                // TODO: 本の画像を入れる $request->getHttpHost().'/storage'.$book->image_path
                $slackClient->postMessage($slackCredential->channel_id, $validated['title'], $validated['message']);
            });
            return response()->json([]);
        } catch (\RuntimeException $e) {
            if ('not_in_channel' === $e->getMessage()) {
                return response()->json(['errors' => [
                    'slack' => 'Slackチャンネルにアプリが追加されていないため通知に失敗しました。',
                ]], 500);
            }
            return response()->json([], 500);
        }
    }
}
