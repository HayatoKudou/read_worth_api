<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\BookCategory;
use App\Models\BookApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\BookApplication\StoreRequest;

class BookApplicationController extends Controller
{
    public function create(string $clientId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);

            return DB::transaction(function () use ($request, $clientId): JsonResponse {
                $request->validated();
                $bookCategory = BookCategory::where('name', $request->bookCategoryName)->firstOrFail();
                $user = User::find(Auth::id());
                @[, $file_data] = explode(';', $request->get('image'));
                @[, $file_data] = explode(',', $request->get('image'));
                $imagePath = '/' . $user->client_id . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
                Storage::put($imagePath, base64_decode($file_data, true));

                $book = Book::create([
                    'client_id' => $clientId,
                    'book_category_id' => $bookCategory->id,
                    'status' => Book::STATUS_APPLYING,
                    'title' => $request->title,
                    'description' => $request->description,
                    'image_path' => $imagePath,
                ]);
                BookApplication::create([
                    'user_id' => $user->id,
                    'client_id' => $clientId,
                    'book_id' => $book->id,
                    'reason' => $request->reason,
                ]);
                return response()->json([], 201);
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
