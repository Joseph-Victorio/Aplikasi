<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVarian;
use Illuminate\Console\Command;

class UpdateVersion240 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update-v-240';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $productVarians = ProductVarian::whereNull('product_id')->get();

        $this->info($productVarians->count());

        if($productVarians->count() > 0) {

            foreach($productVarians as $varian) {

                $parent = $varian->parent;
    
                $varian->product_id = $parent->product_id;
                $varian->save();
             }
             
        }
        
        $products = Product::select('id', 'price', 'weight')->whereHas('varians')
            ->with(['varians' => function($query) {
                $query->whereNotNull('price');
                $query->where('has_subvarian', 0);
            }])->get();

        $this->info($products->count());

        foreach($products as $product) {

            foreach($product->varians as $varian) {

                if($varian->price < $product->price) {
                    $varian->price += $product->price;
                }
                if($varian->weight == 0) {
                    $varian->weight = $product->weight;
                }
                $varian->save();
            }
        }

    }
}
