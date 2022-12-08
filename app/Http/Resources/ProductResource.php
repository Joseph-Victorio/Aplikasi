<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $pricing = [
            'default_price' =>  $this->price,
            'max_price' =>  null,
            'discount_type' => 'PERCENT',
            'discount_amount' => 0,
            'is_discount' => false,
        ];

        if($this->productPromo) {

            $pricing['is_discount'] = true;
            $pricing['discount_type'] = $this->productPromo->discount_type;
            $pricing['discount_amount'] = $this->productPromo->discount_amount;
        }

        if($this->minPrice) {
            $pricing['default_price'] = $this->minPrice->price;
        }
        if($this->maxPrice) {
            $pricing['max_price'] = $this->maxPrice->price;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'pricing' => $pricing,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'stock' => $this->variant_items_sum_item_stock?? $this->stock,
            'description' => $this->description,
            'status' => $this->status,
            'rating' => $this->reviews_avg_rating ? number_format($this->reviews_avg_rating, 1) : 0,
            'weight' => $this->weight,
            'category' => $this->category,
            'assets' => $this->assets,
            'reviews_count' => $this->reviews_count,
            'varians' => $this->varians,
        ];
    }
}
