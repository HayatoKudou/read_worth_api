<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\Client;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Book\CreateRequest;
use App\Http\Requests\Book\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class BookController extends Controller
{
    public function list($clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $books = Book::organization($clientId)->with('purchaseApply')->get();
            $bookCategories = BookCategory::organization($clientId)->get();
            return response()->json([
                'books' => $books->map(fn (Book $book) => [
                    'id' => $book->id,
                    'status' => $book->status,
                    'category' => $book->category->name,
                    'title' => $book->title,
                    'description' => $book->description,
                    'image' => $book->image_path ? base64_encode(Storage::get($book->image_path)) : null,
                    'purchaseApplicant' => $book->purchaseApply?->user,
                    'rentalApplicant' => $book->rentalApply?->user,
                    'reviews' => $book->reviews,
                ]),
                'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                    'name' => $bookCategory->name,
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function update(string $clientId, UpdateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $request->validated();
            $book = Book::find($request->get('id'));
            $bookCategory = BookCategory::where('name', $request->get('category'))->first();
            \Storage::delete($book->image_path);

            if (!$bookCategory) {
                return response()->json('一致するカテゴリが見つかりません', 500);
            }
            $imagePath = $book->storeImage($request->get('image'));
            $book->update([
                'client_id' => $clientId,
                'book_category_id' => $bookCategory->id,
                'status' => $request->get('status'),
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'image_path' => $imagePath,
            ]);
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function create(string $clientId, CreateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $bookCategory = BookCategory::where('name', $request->get('bookCategoryName'))->firstOrFail();
            $book = $request->createBook();
            $imagePath = $book->storeImage($request->get('image'));
            Book::create([
                'client_id' => $clientId,
                'book_category_id' => $bookCategory->id,
                'status' => Book::STATUS_CAN_LEND,
                'title' => $book->title,
                'description' => $book->description,
                'image_path' => $imagePath,
            ]);
            return response()->json([], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
