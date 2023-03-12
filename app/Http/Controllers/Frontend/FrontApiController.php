<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\ApiResponse;
use App\Models\Post;
use App\Models\Block;
use App\Models\Store;
use App\Models\Config;
use App\Models\Slider;
use App\Models\Category;
use App\Models\BankAccount;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Products\ProductRepository;

class FrontApiController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function clearCache()
    {
        Cache::flush();

        return ApiResponse::success();
    }
    public function getInitialData()
    {

        $data['slider_count'] = Cache::rememberForever('slider_count', function () {
            return Slider::count();
        });
        
        $data['blocks'] = Cache::rememberForever('blocks', function () {
            return Block::with('post:id,title,slug')
            ->OrderBy('weight', 'asc')
            ->get();
        });

        $data['shop'] = Cache::rememberForever('shop', function () {
            return Store::first();
        });

        $data['categories'] = Cache::remember('categories',  now()->addSeconds(20), function () {
            return Category::onlyParents()->withCount('childProducts')->get();
        });

        // $data['posts'] = Cache::rememberForever('promote_post', function () {
        //     return Post::promote()->latest()->take(4)->get();
        // });

        $data['config'] = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });

        $data['product_promo'] = Cache::remember('product_promo', now()->addMinutes(2),  function() {

            return $this->productRepository->getProductPromo();

        });

        $data['sess_id'] = Str::random(49);

        return ApiResponse::success($data);
    }

    public function getSliders()
    {
        $data = Cache::rememberForever('sliders', function() {
            return Slider::OrderBy('weight', 'asc')->get();
        });

        return ApiResponse::success($data);
    }

    public function getShop()
    {
        $data['shop'] = Cache::rememberForever('shop', function () {
            return Store::first();
        });
        $data['config'] = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });

        return ApiResponse::success($data);
    }

    public function getCategories()
    {
        $data = Category::withChilds()->get();
            
        return ApiResponse::success($data);
    }

    public function showCategory($id)
    {
        $data = Category::find($id);
            
        return ApiResponse::success($data);
    }

    public function getBlocks()
    {
        $data = Block::with('post:id,slug,title')->orderBy('position', 'desc')->get();

        return ApiResponse::success($data);
    }

    public function showBlock($id)
    {
        $data = Block::find($id)->load('post');

        return ApiResponse::success($data);
    }
    
    public function getConfig()
    {
        $data = Config::first();

        return ApiResponse::success($data);
    }

    public function getBanks()
    {
        $data =  BankAccount::all();

        return ApiResponse::success($data);
    }

    public function getPromotePosts()
    {
        $data = Cache::rememberForever('promote_post', function() {
            return Post::promote()->latest()->get();
        });
        
        return ApiResponse::success($data);
    }

    public function getPosts(Request $request)
    {
        $data = [];
        if($request->query('q') == 'listing'){

            $data = Cache::rememberForever('listing_post', function() {
                return Post::listing()->latest()->get();
            });

        }elseif($request->query('q') == 'promote'){

            $data = Cache::rememberForever('promote_post', function() {
                return Post::promote()->latest()->get();
            });

        } else {

            $data = Cache::rememberForever('all_post', function() {
                return Post::latest()->get();
            });
        }

        return ApiResponse::success($data);
    }
    public function getPostDetail($slug)
    {
        $data = Post::where('slug', $slug)->first();

        return ApiResponse::success($data);
    }
}
