<?php

namespace App\Repositories;

use stdClass;
use Exception;
use App\Models\Asset;
use App\Models\Promo;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;
use App\Models\ProductVarian;
use Ramsey\Uuid\Uuid;

class ProductRepository
{
    protected $limit = 6;
    
    public function show($slug)
    {
        $product = Cache::remember($slug, now()->addMinutes(5), function() use ($slug) {

            return new ProductResource(Product::with(['assets', 'varianItems:id,product_id,label,value,price,sku,stock,varian_id,weight', 'varianAttributes:id,product_id,label,value', 'productPromo' => function($query) {
                $query->whereHas('promoActive');
            }])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('slug', $slug) 
                ->orWhere('id', $slug)
                ->first());
        });


        return $product;
    }

    public function getAll()
    {
            return Product::with(['minPrice','featuredImage', 'category:id,title,slug', 'productPromo' => function($query) {
                $query->whereHas('promoActive');
            }])
            ->withAvg('reviews', 'rating')
            ->simplePaginate($this->limit);
        
    }

    public function getProductsFavorites($pids)
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

    public function getProductsByCategory($id)
    {
        return Product::with(['minPrice','featuredImage', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
            ->where('category_id', $id)
            ->withAvg('reviews', 'rating')
            ->simplePaginate($this->limit);

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
  
    public function store($request)
    {
        $path = public_path('/upload/images');

        if(! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        DB::beginTransaction();
        
        try {
            
            $slug = Str::slug($request->title);
            $product = new Product();

            $product->title = $request->title;
            $product->slug = $slug;
            $product->category_id =  $request->category_id;
            $product->description = $request->description;

            $is_simple_product = $request->boolean('simple_product');

            if($is_simple_product) {

                $product->price = str_replace(".", "", $request->price);
                $product->stock = str_replace(".", "", $request->stock);
                $product->weight = str_replace(".", "", $request->weight);

            }else {

                $product->price = 0;
                $product->stock = 0;
                $product->weight = 0;
            }
              
            $product->save();

            if($request->images && count($request->images) > 0) {

                for($i = 0; $i < count($request->images); $i++) {

                    $file = $request->images[$i];

                    $filename = Str::random(41).'.' . $file->extension();

                    $file->move($path, $filename);

                    $product->assets()->create([
                        'filename' => $filename,
                        'variable' => $i == $request->featured_index ? 'featured' : NULL
                    ]);
                }
            }

            $product->fresh();

            if(!$is_simple_product && $request->varians) {
                $datas = json_decode($request->varians, true);

                foreach($datas as $data) {

                    if($request->boolean('has_subvarian') === true && count($data['subvarian']) > 0) {

                        $varian =  $product->varians()->create([
                                'has_subvarian' => 1,
                                'label' => $data['label'],
                                'value' => $data['value'],
                            ]);
        
                            foreach($data['subvarian'] as $item) {
                                $item['product_id'] = $product->id;
                                $item['price'] = str_replace(".", "", $item['price']);
                                $item['weight'] = str_replace(".", "", $item['weight']);
                                
                                $varian->subvarian()->create($item);
                            }
        
                    } else {

                        $data['price'] = str_replace(".", "", $data['price']);
                        $data['weight'] = str_replace(".", "", $data['weight']);
                        $product->varians()->create($data);
                    }

                } 

                
            }

            DB::commit();

            $this->clearCache();

            return $product->load('featuredImage','varians.subvarian');


        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception($e);
        }

            
    }

    public function update($request)
    {
        $product = Product::find($request->id);

        $path = public_path('/upload/images');

        if(! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        DB::beginTransaction();

        try {

            $is_simple_product = $request->boolean('simple_product');

            $product->title = $request->title;
            $product->description = $request->description;
            $product->category_id = $request->category_id;

            if($is_simple_product) {

                $product->price = str_replace(".", "", $request->price);
                $product->stock = str_replace(".", "", $request->stock);
                $product->weight = str_replace(".", "", $request->weight);

                $product->varians()->delete();

            }

            $product->save();

            if($request->featured_asset) {
                foreach($product->assets as $asset) {

                    if($asset->filename == $request->featured_asset) {
                        $asset->variable = 'featured';
                    }else {
                        $asset->variable = NULL;
                    }
                    $asset->save();
    
                }
            }

            if($request->images && count($request->images) > 0) {

                for($i = 0; $i < count($request->images); $i++) {

                    $isFeatured = false;

                    if(!$request->featured_asset && $i == $request->featured_index) {

                        $isFeatured = true;
                        
                    }

                    $file = $request->images[$i];

                    $filename = Str::random(41).'.' . $file->extension();

                    $file->move($path, $filename);

                    $product->assets()->create([
                        'filename' => $filename,
                        'variable' => $isFeatured? 'featured' : NULL
                    ]);
                }
            }

            if($request->del_images) {
                foreach($request->del_images as $filename) {
                    File::delete('upload/images/'. $filename);
                    Asset::where('filename', $filename)->delete();
                }
            }

            if($request->remove_varian) {
                $varianIds = json_decode($request->remove_varian);

                ProductVarian::whereIn('id', $varianIds)->delete();
            }

            if(!$is_simple_product && $request->varians) {

                $product->stock = 0;
                $product->price = 0;
                $product->weight = 0;
                $product->save();
                
                $datas = json_decode($request->varians, true);

                foreach($datas as $data) {

                    if($request->boolean('has_subvarian') === true) {

                        if(isset($data['id'])) {

                           $varian =  ProductVarian::find($data['id']);

                        }else {

                            $varian =  new ProductVarian();

                        }    
                        
                        $varian->product_id = $product->id;
                        $varian->has_subvarian = 1;
                        $varian->label = $data['label'];
                        $varian->value = $data['value'];
                        $varian->save();
    
                        foreach($data['subvarian'] as $item) {

                            $item['product_id'] = $product->id;
                            $item['price'] = str_replace(".", "", $item['price']);
                            $item['weight'] = str_replace(".", "", $item['weight']);
    
                            if(isset($item['id'])) {

                                ProductVarian::find($item['id'])->update($item);

                            }else {  
                                $varian->subvarian()->create($item);
                            }
                            
                        }
        
                    } else {

                        $data['price'] = str_replace(".", "", $data['price']);
                        $data['weight'] = str_replace(".", "", $data['weight']);

                        if(isset($data['id'])) {

                            ProductVarian::find($data['id'])->update($data);

                        }else {
                            $product->varians()->create($data);
                        }
                        
                    }

                } 
                
            }

            DB::commit();
            
            $product->fresh();
            $product->load('featuredImage', 'category', 'varianItems.parent');

            Cache::forget($product->slug);

            $this->clearCache();

            return $product;

        } catch (Exception $e) {

            DB::rollBack();
            
            throw new Exception($e);
        }

        
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        DB::beginTransaction();

        try {
            if($product->assets) {

                foreach($product->assets as $asset){
                    File::delete('upload/images/'. $asset->filename);
                }
                $product->assets()->delete();
            }
            $product->delete();

            DB::commit();

            $this->clearCache();

            return true;


        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception($e);
        }
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