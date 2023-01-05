<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required',
            'price' => 'required',
            'weight' => 'required',
            'stock' => 'required',
            'description' => 'required',
        ];
        
        if($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $product = Product::find($this->id);

            $imageRule = 'nullable';
            
            if(request()->del_images && count($product->assets) == count(request()->del_images) && !request()->images) {
                $imageRule = 'required';
            }
            
            $rules['images'] = $imageRule;

        }else {
            $rules['title'] = 'required|unique:products';
            $rules['images'] = 'required';
        }

        return $rules;
    }
    public function messages()
    {
        return [
            'title.unique' => 'Nama produk sudah digunakan'
        ];
    }
}
