<?php

namespace App\Models;

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
 * App\Models\Book.
 *
 * @property number $id
 * @property number $client_id
 * @property number $book_category_id
 * @property number $status
 * @property string $title
 * @property string $description
 * @property string $image_path
 * @property string $url
 * @property BookCategory $category
 * @property BookPurchaseApply $purchaseApply
 * @property BookRentalApply $rentalApply
 * @property BookHistory[] $histories
 * @property BookReview[] $reviews
 *
 * @method static Builder|\App\Models\User newModelQuery()
 * @method static Builder|\App\Models\User newQuery()
 * @method static Builder|\App\Models\User query()
 * @method static Builder|\App\Models\User organization()
 * @mixin Builder
 *
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Client $client
 *
 * @method static Builder|Book whereBookCategoryId($value)
 * @method static Builder|Book whereClientId($value)
 * @method static Builder|Book whereCreatedAt($value)
 * @method static Builder|Book whereDescription($value)
 * @method static Builder|Book whereId($value)
 * @method static Builder|Book whereImagePath($value)
 * @method static Builder|Book whereStatus($value)
 * @method static Builder|Book whereTitle($value)
 * @method static Builder|Book whereUpdatedAt($value)
 *
 * @property null|int $reviews_count
 *
 * @method static \Database\Factories\BookFactory factory(...$parameters)
 * @method static Builder|Book whereUrl($value)
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

    public function scopeOrganization(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public static function storeImage(string $imageBinary): string
    {
        $user = User::find(Auth::id());
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
        $imagePath = '/' . $user->client_id . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        Storage::put($imagePath, base64_decode($file_data, true));
        return $imagePath;
    }

    public static function fetchAmazonImage(string $url): string
    {
        $dpStart = mb_strpos($url, 'dp/') + 3;
        $dp = mb_substr($url, $dpStart, 10);
        $type = 'LZZZZZZZ';
        $endpoint = 'https://images-na.ssl-images-amazon.com/images/P/' . $dp . '.09.' . $type;
        $response = Http::get($endpoint);
        $body = $response->getBody()->getContents();
        $img = ('data:image/jpeg;base64,' . base64_encode($body));
        return self::storeImage($img);
    }
}
