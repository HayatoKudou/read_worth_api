<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Book.
 *
 * @property number $id
 * @property number $client_id
 * @property number $book_category_id
 * @property number $status
 * @property string $title
 * @property string $description
 * @property string $image_path
 * @property BookCategory $category
 * @property BookPurchaseApply $purchaseApply
 * @property BookRentalApply $rentalApply
 *
 * @method static Builder|\App\Models\User newModelQuery()
 * @method static Builder|\App\Models\User newQuery()
 * @method static Builder|\App\Models\User query()
 * @method static Builder|\App\Models\User organization()
 * @mixin Builder
 */
class Book extends Model
{
    public const STATUS_CAN_LEND = 1;
    public const STATUS_CAN_NOT_LEND = 2;
    public const STATUS_APPLYING = 3;

    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function purchaseApply(): HasOne
    {
        return $this->hasOne(BookPurchaseApply::class);
    }

    public function rentalApply(): HasOne
    {
        return $this->hasOne(BookRentalApply::class);
    }

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function storeImage(string $imageBinary): string
    {
        $user = User::find(Auth::id());
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
        $imagePath = '/' . $user->client_id . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        Storage::put($imagePath, base64_decode($file_data, true));
        return $imagePath;
    }
}
