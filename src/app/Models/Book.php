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

/**
 * App\Models\Book
 *
 * @property int $id
 * @property int $workspace_id
 * @property int $book_category_id
 * @property int $status
 * @property string $title
 * @property string|null $description
 * @property string|null $image_path
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BookCategory $category
 * @property-read \App\Models\Workspace|null $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BookHistory[] $histories
 * @property-read int|null $histories_count
 * @property-read \App\Models\BookPurchaseApply|null $purchaseApply
 * @property-read \App\Models\BookRentalApply|null $rentalApply
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BookRentalApply[] $rentalHistories
 * @property-read int|null $rental_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BookReview[] $reviews
 * @property-read int|null $reviews_count
 * @method static Builder|Book newModelQuery()
 * @method static Builder|Book newQuery()
 * @method static Builder|Book organization(string $workspaceId)
 * @method static Builder|Book query()
 * @method static Builder|Book whereBookCategoryId($value)
 * @method static Builder|Book whereCreatedAt($value)
 * @method static Builder|Book whereDescription($value)
 * @method static Builder|Book whereId($value)
 * @method static Builder|Book whereImagePath($value)
 * @method static Builder|Book whereStatus($value)
 * @method static Builder|Book whereTitle($value)
 * @method static Builder|Book whereUpdatedAt($value)
 * @method static Builder|Book whereUrl($value)
 * @method static Builder|Book whereWorkspaceId($value)
 * @mixin \Eloquent
 */
class Book extends Model
{
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

    public static function storeImage(string $imageBinary): string
    {
        $user = User::find(Auth::id());
        @[, $file_data] = explode(';', $imageBinary);
        @[, $file_data] = explode(',', $imageBinary);
//        $imagePath = '/public/' . $user->workspace_id . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
        $imagePath = '/' . $user->workspace_id . '/' . $user->id . '/' . Str::random(10) . '.' . 'png';
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
