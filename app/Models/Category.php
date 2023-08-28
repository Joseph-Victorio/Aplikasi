<?php

namespace App\Models;

use App\Scopes\SortWeightOrderedScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_front' => 'boolean',
        'description' => 'string'
    ];

    protected $with = ['image', 'banner'];

    public function image()
    {
        // return $this->assets()->whereNull('variable');
        return $this->morphOne(Asset::class, 'assetable')->whereNull('variable');
    }
    public function banner()
    {
        return $this->morphOne(Asset::class, 'assetable')->where('variable', 'banner');
    }

    public function assets()
    {
        return $this->morphOne(Asset::class, 'assetable');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function childProducts()
    {
        return $this->hasManyThrough(Product::class, Category::class);
    }

    public function scopeOnlyParents($query)
    {
        return $query->whereNull('category_id');
    }

    public function scopeWithChilds($query)
    {
        return $query->whereNull('category_id')->with('childs');
    }

    public function scopeOnlyChilds($query)
    {
        return $query->whereNotNull('category_id');
    }

    public function scopeWithParent($query)
    {
        return $query->whereNotNull('category_id')->with('parent');
    }


    public function scopeHasImage($query)
    {
        return $query->whereNotNull('filename');
    }

    public function childs()
    {
        return $this->hasMany(Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function productItems()
    {
        return $this->hasMany(Product::class)->take(2);
    }

    public function productReviewRatings()
    {
        return $this->hasManyThrough(Review::class, Product::class)->avg('rating');
    }

    protected static function booted()
    {
        static::addGlobalScope(new SortWeightOrderedScope);
    }
}
