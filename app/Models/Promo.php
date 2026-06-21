<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

protected $fillable = [
    'seller_id',
    'category_id',
    'title',
    'description',
    'poster_image',
    'discount_percentage',
    'original_price',
    'promo_price',
    'start_date',
    'end_date',
    'status',
    'is_premium'
];

protected function casts(): array
{
    return [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_hot_deal' => 'boolean',
        'is_premium' => 'boolean',
    ];
}

    // ─── Relationships ────────────────────────────────────────────────────────

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

   public function getPosterUrlAttribute()
{
    return $this->poster_image
        ? asset('storage/' . $this->poster_image)
        : asset('images/no-image.png');
}
    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeHotDeals(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where('end_date', '<=', now()->addHours(48))
                     ->where('end_date', '>=', now());
    }
}