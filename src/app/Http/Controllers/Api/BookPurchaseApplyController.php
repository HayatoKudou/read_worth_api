<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\User;
use App\Models\Client;
use App\Models\BookHistory;
use App\Models\BookCategory;
use App\Models\BookPurchaseApply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BookPurchaseApply\DoneRequest;
use App\Http\Requests\BookPurchaseApply\CreateRequest;

class BookPurchaseApplyController extends Controller
{
    public function list(string $clientId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        $bookPurchaseApplies = BookPurchaseApply::where('client_id', $clientId)->get();
        return response()->json([
            'bookPurchaseApplies' => $bookPurchaseApplies->map(fn (BookPurchaseApply $bookPurchaseApply) => [
                'reason' => $bookPurchaseApply->reason,
                'price' => $bookPurchaseApply->price,
                'step' => $bookPurchaseApply->step,
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

        DB::transaction(function () use ($request, $clientId): void {
            $user = User::find(Auth::id());
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
                'step' => BookPurchaseApply::NEED_ALLOW,
            ]);
            BookHistory::create([
                'book_id' => $book->id,
                'user_id' => Auth::id(),
                'action' => 'purchase book',
            ]);
        });
        return response()->json([], 201);
    }

    public function allow(string $clientId, string $bookId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        Book::find($bookId)->purchaseApply->update([
            'step' => BookPurchaseApply::NEED_BUY,
        ]);
        return response()->json([]);
    }

    public function done(string $clientId, string $bookId, DoneRequest $request): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        Book::find($bookId)->purchaseApply->update([
            'step' => BookPurchaseApply::NEED_NOTIFICATION,
            'location' => $request->get('location'),
        ]);
        return response()->json([]);
    }

    public function reject(string $clientId, string $bookId): JsonResponse
    {
        $client = Client::find($clientId);
        $this->authorize('affiliation', $client);
        Book::find($bookId)->purchaseApply->update([
            'step' => BookPurchaseApply::REJECTED,
        ]);
        return response()->json([]);
    }
}
