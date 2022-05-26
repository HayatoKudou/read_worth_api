<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
            $books = Book::organization($clientId)->get();
            $bookCategories = BookCategory::organization($clientId)->get();
            return response()->json([
                'books' => $books->map(fn (Book $book) => [
                    'title' => $book->title,
                    'description' => $book->description,
                    'image' => $book->image_path ? base64_encode(Storage::get($book->image_path)) : null,
                ]),
                'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                    'name' => $bookCategory->name,
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function create(string $clientId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            @[, $file_data] = explode(';', $request->get('image'));
            @[, $file_data] = explode(',', $request->get('image'));
            $book = $request->makePost();
            $user = User::find(Auth::id());
            $imagePath = '/' . $user->client_id . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
            Storage::put($imagePath, base64_decode($file_data, true));
            Book::create([
                'client_id' => $clientId,
                'book_category_id' => $book->book_category_id,
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
