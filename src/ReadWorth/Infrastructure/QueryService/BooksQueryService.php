<?php

namespace ReadWorth\Infrastructure\QueryService;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookReview;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BooksQueryService
{
    public function getBooks($workspaceId): array
    {
        \DB::enableQueryLog();
        $books = Book::organization($workspaceId)
            ->with('category')
            ->with('purchaseApply.user')
            ->with('rentalApply.user')
            ->with('reviews')
            ->with('rentalApplies')
            ->get();

        $bookCategories = BookCategory::organization($workspaceId)->get();
        $a =  [
            'books' => $books->map(fn (Book $book) => [
                'id' => $book->id,
                'status' => $book->status,
                'category' => $book->category->name,
                'title' => $book->title,
                'description' => $book->description,
                'image' => $book->image_path ? base64_encode(Storage::get($book->image_path)) : null,
                'url' => $book->url,
                'createdAt' => Carbon::parse($book->created_at)->format('Y/m/d'),
                'purchaseApplicant' => [
                    'id' => $book->purchaseApply?->user->id,
                    'name' => $book->purchaseApply?->user->name,
                ],
                'rentalApplicant' => $book->rentalApply ? [
                    'id' => $book->rentalApply->user->id,
                    'name' => $book->rentalApply->user->name,
                    'expectedReturnDate' => $book->rentalApply->expected_return_date,
                ] : null,
                'reviews' => collect($book->reviews)?->map(fn (BookReview $bookReview) => [
                    'rate' => $bookReview->rate,
                    'review' => $bookReview->review,
                    'reviewedAt' => Carbon::parse($bookReview->created_at)->format('Y年m月d日 H時i分'),
                    'reviewer' => $bookReview->user->name,
                ]),
                'rentalCount' => $book->rentalApplies->count(),
            ]),
            'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                'name' => $bookCategory->name,
            ]),
        ];
        \Log::debug(\DB::getQueryLog());
        return $a;
    }
}
