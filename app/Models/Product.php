<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid as Generator; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' > 'boolean',
        'category_id' => 'integer',
        'price' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->sku = Generator::uuid4()->toString();
            } catch (\Exception $e) {
                $model->sku = Str::upper(Str::random(32));
                Log::info($e->getMessage());
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function assets()
    {
        return $this->morphMany(Asset::class, 'assetable')->orderByDesc('variable');
    }
    public function minPrice()
    {
        return $this->hasOne(ProductVarian::class)
            ->where('has_subvarian', 0)
            ->whereNotNull('price')
            ->orderBy('price');
    }
    public function maxPrice()
    {
        return $this->hasOne(ProductVarian::class)
            ->where('has_subvarian', 0)
            ->whereNotNull('price')
            ->orderByDesc('price');
    }
    public function featuredImage()
    {
        return $this->morphOne(Asset::class, 'assetable')->orderByDesc('variable');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest()->where('is_approved', 1);
    }
    public function allReviews()
    {
        return $this->hasMany(Review::class)->latest();
    }
    public function reviewsLimit()
    {
        return $this->hasMany(Review::class)->take(4);
    }
    public function productRating()
    {
        return $this->hasMany(Review::class)->avg('rating');
    }
    public function promo()
    {
        return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id')
            ->withPivot('discount_type', 'discount_amount')
            ->where('end_date', '>', now());
    }
    public function promoRelations()
    {
        return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id');
    }
    public function productPromo()
    {
        return $this->hasOne(ProductPromo::class, 'product_id', 'id');
    }
    public function varians()
    {
        return $this->hasMany(ProductVarian::class)
            ->whereNull('varian_id')
            ->orderBy('price');
    }
    public function varianAttributes()
    {
        return $this->hasMany(ProductVarian::class)
            ->where('has_subvarian', 1)
            ->orderBy('label');
    }
    public function varianItems()
    {
        return $this->hasMany(ProductVarian::class)
            ->where('has_subvarian', 0);
    }
    public function varianItemSortByPrice()
    {
        return $this->hasMany(ProductVarian::class)
            ->where('has_subvarian', 0)
            ->orderBy('price');
    }
    
}
