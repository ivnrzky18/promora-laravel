<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'business_category',
        'description',
        'address',
        'latitude',
        'longitude',
        'logo',
        'is_verified',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class, 'seller_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'seller_id');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscription::class, 'seller_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function averageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0.0;
    }
}
