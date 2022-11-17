<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class FrontController extends Controller
{
    public $shop;

    public function __construct()
    {
        $this->shop = Cache::rememberForever('shop', function() {
            return Store::first();
        });
    }

    public function homepage()
    {
        $title = $this->shop->name;

        if($this->shop->slogan) {
            $title = $title . ' | ' . $this->shop->slogan;
        }
        return View::vue([
            'title' => $title,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url($this->shop->logo_path) : null,
        ]);
    }
    
    public function products()
    {
        return View::vue([
            'title' => 'Produk Katalog | ' . $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url($this->shop->logo_path) : null,
        ]);
    }
    public function productDetail($slug)
    {
       $product =  Product::with(['assets'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug) 
            ->first();

        $featuredImage = $product->assets[0]->src;
        $desc= $product->description ? $this->createTeaser($product->description) : $this->shop->description;

        $schema = $this->getSingleProductSchema($product);

        return View::vue([
            'title' => $product->title . ' | ' . $this->shop->name,
            'description' => $desc,
            'featured_image' => $featuredImage,
            'json_schema' => $schema
        ]);

    }

    public function productCategory(Category $category)
    {
        return View::vue([
            'title' => $category->title . ' | ' . $this->shop->name,
            'description' => $category->description?? $this->shop->description,
            'featured_image' => url('/upload/images/' . $category->filename),
        ]);

    }
    public function postIndex()
    {
        return View::vue([
            'title' => 'Artikel | ' . $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url($this->shop->logo_path) : null,
        ]);
    }
    public function postDetail($slug)
    {
        $post = Post::where('slug', $slug)->first();
        
        return View::vue([
            'title' => $post->title . ' | ' . $this->shop->name,
            'description' => $this->createTeaser($post->body),
            'featured_image' => url('/upload/images/' . $post->image),
        ]);
    }
    public function showInvoice($id)
    {
        return View::vue([
            'title' => "Invoice #$id",
            'description' => "Detail tagihan dan instruksi pembayaran invoice #$id - " . $this->shop->name,
        ]);
    }
    public function any()
    {
        $title = $this->shop->name;
        if($this->shop->slogan) {
            $title = $title . ' | ' . $this->shop->slogan;
        }
        return View::vue([
            'title' => $title,
        ]);
    }
    public function clearCache()
    {   
        Cache::flush();
        return redirect('/');
    }
    protected function createTeaser($html)
    {
        $str = strip_tags($html);

        return substr($str, 0, 155); 
    }
    public function sitemap()
    {
        $categories = Category::select('id', 'slug', 'updated_at')->get();
        $products = Product::select('id', 'slug', 'updated_at')->get();
        return response()->view('sitemap', compact('categories', 'products'))
                ->header('Content-Type', 'text/xml');
        
    }
    protected function getSingleProductSchema($product)
    {
        $featuredImage = $product->assets[0]->src;
        $desc= $product->description ? $this->createTeaser($product->description) : $this->shop->description;
        $rating= $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : 0;

        $data = [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "description" => $desc,
            "name" => $product->title,
            "image" => $featuredImage,
            "offers" => [
              "@type" => "Offer",
              "availability" => "https://schema.org/InStock",
              "price" => $product->price,
              "priceCurrency" => "IDR"
            ],
            "aggregateRating" => [
              "@type" => "AggregateRating",
              "ratingValue" => "$rating",
              "reviewCount" => "$product->reviews_count",
            ],
            "review" => [
              "@type" => "Review",
              "reviewRating" => [
                "@type" => "Rating",
                "ratingValue" => "$rating",
                "bestRating" => "5"
              ]
            ]
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}
