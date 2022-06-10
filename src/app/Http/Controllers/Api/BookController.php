<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\Client;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Book\StoreRequest;
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
                ]),
                'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                    'name' => $bookCategory->name,
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function update(string $clientId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $request->validated();
            $book = Book::find($request->get('id'));
            $imagePath = $book->storeImage($request->get('image'));
            $bookCategory = BookCategory::where('name', $request->get('category'))->firstOrFail();
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

    public function create(string $clientId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $request->validated();
            $bookCategory = BookCategory::where('name', $request->get('bookCategoryName'))->firstOrFail();
            $book = new Book();
            $imagePath = $book->storeImage($request->get('image'));
            Book::create([
                'client_id' => $clientId,
                'book_category_id' => $bookCategory->id,
                'status' => Book::STATUS_CAN_LEND,
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'image_path' => $imagePath,
            ]);
            return response()->json([], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
