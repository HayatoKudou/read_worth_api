<?php

namespace ReadWorth\Application\UseCase\Books;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreBookImage
{
    public function store(string $imageBinary, string $workspaceId): string
    {
        $user = Auth::user();
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
        $imagePath = $workspaceId . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        // URL例: http://localhost:8000/storage/1/3003/mIv2eG0Lgr.png
        Storage::put($imagePath, base64_decode($file_data, true));
        return $imagePath;
    }
}
