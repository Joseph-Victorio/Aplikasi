<?php

namespace App\Repositories\Products;

use Exception;
use App\Models\Asset;
use App\Models\Promo;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductPromo;
use App\Models\ProductVarian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;


class AdminProductRepository
{
    
  	public function index($limit)
    {
        return Product::with(['minPrice:id,product_id,price', 'maxPrice:id,product_id,price','featuredImage'])
			->withCount('varianItems')
			->withSum('varianItems', 'stock')
			->latest()
			->paginate($limit);
	}
	public function show($id)
    {
		return Product::with('assets', 'category', 'varians.subvarian')
		->where('id', $id) 
		->first();          
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

            throw $e;
        }

            
    }

    public function update($request, $id)
    {

        $path = public_path('/upload/images');

        if(! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $product = Product::find($id);

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

                        $product->assets()->update([
                            'variable' => NULL
                        ]);
                        
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
            $product->load('featuredImage')->loadCount('varianItems')->loadSum('varianItems', 'stock');

            Cache::forget($product->slug);

            $this->clearCache();

            return $product;

        } catch (Exception $e) {

            DB::rollBack();
            
            throw $e;
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

            ProductPromo::where('product_id', $product->id)->delete();
            
            $product->delete();

            DB::commit();

            $this->clearCache();

            return true;


        } catch (Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

	public function productVarianByProductId($productId)
    {
		return ProductVarian::leftJoin('product_varians as parent', 'product_varians.varian_id', 'parent.id')
		->select('parent.label as attribute_label', 'parent.value as attribute_value', 'product_varians.label','product_varians.value', 'product_varians.price','product_varians.stock')
		->where('product_varians.product_id', $productId)
        ->where('product_varians.has_subvarian', 0)
		->orderBy('attribute_value')
		->orderBy('product_varians.price')
		->get();

    }

	public function search($keyword, $limit = 20)
    {
		return Product::where('title', 'like', '%'.$keyword.'%')
			->with(['minPrice', 'maxPrice','featuredImage', 'category', 'varianItems.parent'])
			->paginate($limit);
    }

	public function findProductWithoutPromo($request)
    {
        
		return Product::doesntHave('promoRelations')
            ->when($request->category_id, function($q) use ($request) {
                $ids = Category::where('category_id', $request->category_id)->select('id')->pluck('id')->toArray();
                array_push($ids, $request->category_id);
                $q->whereIn('category_id', $ids);
            })
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', '%'. $request->search . '%');
            })
			->get();
    }

	public function getProductPromo($promoId)
    {
        
		try {

			$promo = Promo::findOrFail($promoId);
	
			return $promo->products;

        } catch (Exception $e) {

            throw $e;
        }

    }

	public function storeProductPromo($data)
    {

		$product = ProductPromo::updateOrCreate([
			'product_id' => $data['product_id']
		], $data);
		
		$this->clearCache();

		return $product;

    }

	protected function clearCache()
    {
        Cache::forget('products');
        Cache::forget('initial_products');
        Cache::forget('product_promo');
    }

}