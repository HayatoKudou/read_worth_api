<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Client;
use App\Models\User;
use App\Http\Requests\Book\StoreRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function list($clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $books = Book::organization($clientId)->get();
            return response()->json([
                'books' => $books->map(fn (Book $book) => [
                    'title' => $book->title,
                    'description' => $book->description,
                    'image' => $book->image_path ? base64_encode(Storage::get($book->image_path)) : null,
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
            @list(, $file_data) = explode(';', $request->get("image"));
            @list(, $file_data) = explode(',', $request->get("image"));
            $book = $request->makePost();
            $user = User::find(Auth::id());
            $imagePath = '/'.$user->client_id.'/'.$user->id.'/'. Str::random(10).'.'.'png';
            Storage::put($imagePath, base64_decode($file_data));
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
