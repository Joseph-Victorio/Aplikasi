<?php

namespace App\Repositories\Products;

use stdClass;
use App\Models\Promo;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductListCollection;

class ProductRepository
{
    
    public function getSingleProduct($slug)
    {
        $product = Cache::remember($slug, now()->addHours(3), function() use ($slug) {

            return Product::with(['assets', 'varianItemSortByPrice:id,product_id,label,value,price,sku,stock,varian_id,weight', 'varianAttributes:id,product_id,label,value', 'productPromo' => function($query) {
                $query->whereHas('promoActive');
            }])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('slug', $slug) 
                ->orWhere('id', $slug)
                ->first();
        });


        return $product;
    }

    public function getAll($per_page, $order_by)
    {
        $instance  = Product::query();

        if($order_by == 'RANDOM') {
            $instance->inRandomOrder();
        }else {
            $instance->orderBy('id', $order_by);
        }

       $data = $instance->with(['minPrice','featuredImage', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
        ->withAvg('reviews', 'rating')
        ->simplePaginate($per_page);

        return $data;
    
    }

    public function getManyInId($pids)
    {
        return Product::with(['minPrice','featuredImage', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
            ->whereIn('id', $pids)
            ->withAvg('reviews', 'rating')
            ->get();

    }
  
    public function search($key)
    {

        return Product::with(['minPrice','featuredImage', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
            ->where('title', 'like', '%'.$key.'%')
            ->withAvg('reviews', 'rating')
            ->get();

    }

    public function getProductByCategory($id, $per_page = 10, $offset = 0, $order_by = 'DESC', $is_subcategory = false)
    {


        try {

            $ids = [$id];

            $category = Cache::remember('category-'. $id, now()->addHours(3) , function() use ($id) {
                return Category::select('id','title', 'slug', 'weight')->where('id', $id)->firstOrFail();
            });

            if(!$is_subcategory) {
                $cids = Category::where('category_id', $id)->select('id')->pluck('id')->toArray();
                $ids = array_merge($ids, $cids);
            }
    
            $instance  = Product::query();
    
            if($order_by == 'RANDOM') {
                $instance->inRandomOrder();
            }else {
                $instance->orderBy('id', $order_by);
            }
        
            $data = $instance->with(['minPrice','featuredImage', 'category:id,title,slug', 'productPromo' => function($query) {
                    $query->whereHas('promoActive');
                }])
                ->whereIn('category_id', $ids)
                ->withAvg('reviews', 'rating')
                ->take($per_page)
                ->offset($offset)
                ->get();
    
            return [
                'category' => $category,
                'data' => new ProductListCollection($data),
                'limit' => $per_page,
                'offset' => $offset,
                'total' => $instance->count(),
            ];

        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function getProductPromo()
    {
        return Promo::active()->with(['products' => function($query) {
            $query->with('minPrice');
            $query->with('featuredImage');
            $query->with('productPromo', function($q) {
                $q->whereHas('promoActive');
            });
            $query->withAvg('reviews', 'rating');
        }])
        ->whereHas('products')
        ->get()->map(function($item) {

            $promo = new stdClass();
            $promo->id = $item->id;
            $promo->label = $item->label;
            $promo->start_date = $item->start_date;
            $promo->end_date = $item->end_date;

            $promo->products = $item->products->map(function($product) {

                return [
                    'id'      => $product->id,
                    'title'   => $product->title,
                    'slug'    => $product->slug,
                    'status'  =>  $product->status,
                    'rating'  =>  $product->reviews_avg_rating ? (float) number_format($product->reviews_avg_rating, 1) : 0,
                    'pricing' =>  $this->setPricing($product),
                    'asset'  =>  $product->featuredImage,
                ];
            });

            return $promo;

        });
        
    }

    public function getInitialProducts()
    {

        $data = Category::whereHas('products')
            ->with(['products' => function($query) {
                $query->with('minPrice');
                $query->with('featuredImage');
                $query->with('productPromo', function($q) {
                    $q->whereHas('promoActive');
                });
                $query->with('varians.subvarian');
                $query->withAvg('reviews', 'rating');
            }])
            ->where('is_front', 1)
            ->orderBy('weight')
            ->get()
            ->map(function($cat) {

                $categoryItem = new stdClass();
                $categoryItem->title = $cat->title;
                $categoryItem->category_id = $cat->id;
                $categoryItem->category_slug = $cat->slug;
                $categoryItem->id = Str::random(16);
                $categoryItem->description = $cat->description ?? '';
                $categoryItem->banner_src = $cat->banner ? url('upload/images/' . $cat->banner) : '';

                $categoryItem->items = $cat->products->map(function($product) use($cat) {

                    // Prevent recursive loop
                    $newCat = new stdClass();
                    $newCat->id = $cat->id;
                    $newCat->title = $cat->title;
                    $newCat->slug = $cat->slug;

                    $product->category = $newCat;

                    return [
                        'id'      => $product->id,
                        'title'   => $product->title,
                        'sku'   => $product->sku,
                        'slug'    => $product->slug,
                        'status'  =>  $product->status,
                        'rating'  =>  $product->reviews_avg_rating ? (float) number_format($product->reviews_avg_rating, 1) : 0,
                        'pricing' =>  $this->setPricing($product),
                        'category' => $newCat,
                        'asset'  =>  $product->featuredImage,
                        'description' =>  $product->description,
                    ];
                });

                return $categoryItem;
            });

        return $data;

    }

    protected function setPricing($product)
    {
        $defaultPrice = $product->price;

            $pricing = [
                'default_price' => $defaultPrice,
                'discount_type' => 'PERCENT',
                'discount_amount' => 0,
                'is_discount' => false,
            ];
    
            if($product->productPromo) {

                $disc = $product->productPromo;

                $pricing['is_discount'] = true;
                $pricing['discount_type'] = $disc->discount_type;
                $pricing['discount_amount'] = $disc->discount_amount;
            }

            if($product->minPrice) {
                $pricing['default_price'] = $product->minPrice->price;
            }
        
            return $pricing;
    }
    
    protected function clearCache()
    {
        Cache::forget('products');
        Cache::forget('initial_products');
        Cache::forget('product_promo');
    }

}