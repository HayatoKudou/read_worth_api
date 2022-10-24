<?php

namespace ReadWorth\Application\UseCase;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreBookImage
{
    public static function store(string $imageBinary, string $workspaceId): string
    {
        $user = Auth::user();
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
        $imagePath = $workspaceId . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        Storage::put($imagePath, base64_decode($file_data, true));
        return $imagePath;
    }
}
