<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use App\Models\Block;
use App\Models\Store;
use App\Models\Config;
use App\Models\Slider;
use App\Models\Category;
use App\Models\BankAccount;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ProductRepository;

class FrontApiController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function homepage()
    {

        $sliders = Cache::rememberForever('sliders', function () {
            return Slider::OrderBy('weight', 'asc')->get();
        });
        
        $blocks = Cache::rememberForever('blocks', function () {
            return Block::with('post:id,title,slug')
            ->OrderBy('weight', 'asc')
            ->get();
        });

        $shop = Cache::rememberForever('shop', function () {
            return Store::first();
        });

        $categories = Cache::remember('categories',  now()->addSeconds(20), function () {
            return Category::select('id','banner', 'filename', 'slug', 'description', 'is_front')->orderBy('weight', 'asc')->withCount('products')->get();
        });

        $posts = Cache::rememberForever('promote_post', function () {
            return Post::promote()->latest()->take(4)->get();
        });

        $config = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });

        $productPromo = Cache::remember('product_promo', now()->addMinutes(2),  function() {

            return $this->productRepository->getProductPromo();

        });

        // $initialProducts = Cache::rememberForever('initial_products', function() {
            
        //     return $this->productRepository->getInitialProducts();
        // });

        return response()->json([
            'success' => true, 
            'results' => [
                'sliders' => $sliders,
                'categories' => $categories,
                'blocks' => $blocks,
                'shop' => $shop,
                'posts' => $posts,
                'config' => $config,
                'sess_id' => Str::random(49),
                'product_promo' => $productPromo,
            ]
            
        ],200);
    }

    public function getSliders()
    {
        return response([
            'success' => true,
            'results' =>  Slider::OrderBy('weight', 'asc')->get()
        ], 200);
    }

    public function getShop()
    {
        $shop = Cache::rememberForever('shop', function () {
            return Store::first();
        });
        $config = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });
        return response([
            'success' => true,
            'results' => [
                'shop' => $shop,
                'config' => $config
            ]
        ], 200);
    }

    public function getCategories()
    {
        return response([
            'success' => true, 
            'results' => Category::orderBy('weight', 'asc')->get()
            
        ],200);
    }

    public function showCategory($id)
    {
        return response([
            'success' => true, 
            'results' => Category::find($id)
            
        ],200);
    }

    public function getBlocks()
    {
        return response()->json([
            'success' => true,
            'results' => Block::with('post:id,slug,title')->orderBy('position', 'desc')->get()
        ]);
    }

    public function showBlock($id)
    {
        return response()->json([
            'success' => true,
            'results' => Block::find($id)->load('post')
        ]);
    }
    
    public function getConfig()
    {
        return response([
            'success' => true,
            'results' => Config::first()
        ], 200);
    }

    public function getBanks()
    {
        return response(['success' => true, 'results' => BankAccount::all()], 200);
    }

    public function getPromotePosts()
    {
        $posts = Cache::rememberForever('promote_post', function() {
            return Post::promote()->latest()->get();
        });
        
        return response()->json([
            'success' => true,
            'results' => $posts
        ]);
    }

    public function getPosts(Request $request)
    {
        $posts = [];
        if($request->query('q') == 'listing'){

            $posts = Cache::rememberForever('listing_post', function() {
                return Post::listing()->latest()->get();
            });

        }elseif($request->query('q') == 'promote'){

            $posts = Cache::rememberForever('promote_post', function() {
                return Post::promote()->latest()->get();
            });

        } else {

            $posts = Cache::rememberForever('all_post', function() {
                return Post::latest()->get();
            });
        }

        return response()->json([
            'success' => true,
            'results' => $posts
        ]);
    }
    public function getPostDetail($slug)
    {
        return response()->json([
            'success' => true,
            'results' => Post::where('slug', $slug)->first()
        ]);
    }
}
