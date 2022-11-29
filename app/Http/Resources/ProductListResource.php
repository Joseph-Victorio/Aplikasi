<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
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
        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'slug'    => $this->slug,
            'sku'    => $this->sku,
            'description' =>  $this->description,
            'status'  =>  $this->status,
            'rating'  =>  $this->reviews_avg_rating ? (float) number_format($this->reviews_avg_rating, 1) : 0,
            'pricing' =>  $this->setPricing($this),
            'category' => $this->category,
            'asset'  =>  $this->featuredImage,
            'category_id' => $this->category_id,
            'category_type' => $this->category_type,
          ];
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
        
        return $pricing;
    }
}
