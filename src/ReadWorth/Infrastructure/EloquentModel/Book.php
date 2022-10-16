<?php

namespace ReadWorth\Infrastructure\EloquentModel;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ReadWorth\Infrastructure\EloquentModel\Book.
 *
 * @property int $id
 * @property int $workspace_id
 * @property int $book_category_id
 * @property int $status
 * @property string $title
 * @property null|string $description
 * @property null|string $image_path
 * @property null|string $url
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \ReadWorth\Infrastructure\EloquentModel\BookCategory $category
 * @property null|\ReadWorth\Infrastructure\EloquentModel\Workspace $client
 * @property \Illuminate\Database\Eloquent\Collection|\ReadWorth\Infrastructure\EloquentModel\BookHistory[] $histories
 * @property null|\ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply $purchaseApply
 * @property null|\ReadWorth\Infrastructure\EloquentModel\BookRentalApply $rentalApply
 * @property \Illuminate\Database\Eloquent\Collection|\ReadWorth\Infrastructure\EloquentModel\BookRentalApply[] $rentalHistories
 * @property \Illuminate\Database\Eloquent\Collection|\ReadWorth\Infrastructure\EloquentModel\BookReview[] $reviews
 *
 * @method static Builder|Book newModelQuery()
 * @method static Builder|Book newQuery()
 * @method static Builder|Book organization(string $workspaceId)
 * @method static Builder|Book query()
 * @mixin \Eloquent
 */
class Book extends Model
{
    use HasFactory;
    public const STATUS_CAN_LEND = 1;
    public const STATUS_CAN_NOT_LEND = 2;
    public const STATUS_APPLYING = 3;

    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
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
        return $this->hasOne(BookRentalApply::class)->whereNull('return_date');
    }

    public function rentalHistories(): HasMany
    {
        return $this->hasMany(BookRentalApply::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BookHistory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    public function scopeOrganization(Builder $query, string $workspaceId): Builder
    {
        return $query->where('workspace_id', $workspaceId);
    }

    public static function storeImage(string $imageBinary, string $workspaceId): string
    {
        $user = Auth::user();
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
        $imagePath = $workspaceId . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        Storage::put($imagePath, base64_decode($file_data, true));
        return $imagePath;
    }

    public static function fetchAmazonImage(string $url, string $workspaceId): string
    {
        $dpStart = mb_strpos($url, 'dp/') + 3;
        $dp = mb_substr($url, $dpStart, 10);
        $type = 'LZZZZZZZ';
        $endpoint = 'https://images-na.ssl-images-amazon.com/images/P/' . $dp . '.09.' . $type;
        $response = Http::get($endpoint);
        $body = $response->getBody()->getContents();
        $img = ('data:image/jpeg;base64,' . base64_encode($body));
        return self::storeImage($img, $workspaceId);
    }
}
