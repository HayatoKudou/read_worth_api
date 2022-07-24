<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\User;
use App\Models\Client;
use App\Models\BookHistory;
use App\Models\BookCategory;
use App\Slack\SlackApiClient;
use App\Models\SlackCredential;
use App\Models\BookPurchaseApply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BookPurchaseApply\DoneRequest;
use App\Http\Requests\BookPurchaseApply\CreateRequest;
use App\Http\Requests\BookPurchaseApply\NotificationRequest;

class BookPurchaseApplyController extends Controller
{
    public function list(string $clientId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        $bookPurchaseApplies = BookPurchaseApply::where('client_id', $clientId)->get();
        return response()->json([
            'slackCredentialExists' => (bool) $client->slackCredential,
            'bookPurchaseApplies' => $bookPurchaseApplies->map(fn (BookPurchaseApply $bookPurchaseApply) => [
                'reason' => $bookPurchaseApply->reason,
                'price' => $bookPurchaseApply->price,
                'step' => $bookPurchaseApply->step,
                'location' => $bookPurchaseApply->location,
                'createdAt' => Carbon::parse($bookPurchaseApply->created_at)->format('Y/m/d'),
                'user' => $bookPurchaseApply->user,
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

    public function create(string $clientId, CreateRequest $request): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        $user = User::find(Auth::id());

        DB::transaction(function () use ($user, $request, $clientId): void {
            $book = new Book();
            $imagePath = $book->storeImage($request->get('image'));
            $bookCategory = BookCategory::where('name', $request->get('bookCategoryName'))->firstOrFail();

            $book = Book::create([
                'client_id' => $clientId,
                'book_category_id' => $bookCategory->id,
                'status' => Book::STATUS_APPLYING,
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'url' => $request->get('url'),
                'image_path' => $imagePath,
            ]);
            BookPurchaseApply::create([
                'user_id' => $user->id,
                'client_id' => $clientId,
                'book_id' => $book->id,
                'reason' => $request->get('reason'),
                'price' => $request->get('price'),
                'step' => BookPurchaseApply::NEED_ACCEPT,
            ]);
            BookHistory::create([
                'book_id' => $book->id,
                'user_id' => Auth::id(),
                'action' => 'purchase book',
            ]);
        });

        try {
            $title = '書籍購入申請のお知らせ';
            $message = '【タイトル】' . $request->get('title') . "\n【申請者】" . $user->name;
            $slackCredential = SlackCredential::where('client_id', $clientId)->first();
            if($slackCredential){
                $slackClient = new SlackApiClient(new \GuzzleHttp\Client(), $slackCredential->access_token);
                $slackClient->postMessage($slackCredential->channel_id, $title, $message);
            }
            return response()->json([], 201);
        } catch (\RuntimeException $e) {
            // 購入申請のSlack通知エラーは無視する
            return response()->json([], 201);
        }
    }

    public function accept(string $clientId, string $bookId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        DB::transaction(function () use ($bookId): void {
            Book::find($bookId)->purchaseApply->update([
                'step' => BookPurchaseApply::NEED_BUY,
            ]);
            BookHistory::create([
                'book_id' => $bookId,
                'user_id' => Auth::id(),
                'action' => 'purchase accepted',
            ]);
        });
        return response()->json([]);
    }

    public function done(string $clientId, string $bookId, DoneRequest $request): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        DB::transaction(function () use ($request, $bookId): void {
            $book = Book::find($bookId);
            $book->update(['status' => Book::STATUS_CAN_LEND]);
            $book->purchaseApply->update([
                'step' => BookPurchaseApply::NEED_NOTIFICATION,
                'location' => $request->get('location'),
            ]);
            BookHistory::create([
                'book_id' => $bookId,
                'user_id' => Auth::id(),
                'action' => 'purchase done',
            ]);
        });
        return response()->json([]);
    }

    public function refuse(string $clientId, string $bookId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        DB::transaction(function () use ($bookId): void {
            Book::find($bookId)->purchaseApply->update([
                'step' => BookPurchaseApply::REFUSED,
            ]);
            BookHistory::create([
                'book_id' => $bookId,
                'user_id' => Auth::id(),
                'action' => 'purchase refused',
            ]);
        });

        return response()->json([]);
    }

    public function init(string $clientId, string $bookId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
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

    public function notification(string $clientId, string $bookId, NotificationRequest $request): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);

        try {
            DB::transaction(function () use ($clientId, $bookId, $request) {
                $book = Book::find($bookId);
                $book->purchaseApply->delete();

                // 通知が失敗したらロールバック
                $slackCredential = SlackCredential::where('client_id', $clientId)->first();
                if(!$slackCredential){
                    return response()->json(['errors' => [
                        'slack' => 'Slack連携がされていません。',
                    ]], 500);
                }
                $slackClient = new SlackApiClient(new \GuzzleHttp\Client(), $slackCredential->access_token);
                // TODO: 本の画像を入れる $request->getHttpHost().'/storage'.$book->image_path
                $slackClient->postMessage($slackCredential->channel_id, $request->get('title'), $request->get('message'));
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
