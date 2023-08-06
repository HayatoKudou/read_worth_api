<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\User;

class LpController extends Controller
{
    public function getTotalCount(): JsonResponse
    {
        $userCount = User::all()->count();
        $bookCount = Book::all()->count();
        return response()->json([
            'userCount' => $userCount,
            'bookCount' => $bookCount,
        ]);
    }
}
