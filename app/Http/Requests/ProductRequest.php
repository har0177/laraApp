<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            //
            'name' => 'required',
            'cat_id' => 'required',
            'pprice' => 'required|numeric',
            'sprice' => 'required|numeric',
            'wprice' => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            //
            'name' => 'Product Name',
            'cat_id' => 'Category',
            'pprice' => 'Purchase Price',
            'sprice'=> 'Sale Price',
            'wprice' => 'Wholesale Price'
        ];
    }

    public function messages()
    {
        return [
            //
            'name.required' => 'Product Name is required',
            'pprice.required' => 'Purchase Price is required',
            'sprice.required' => 'Sale Price is required',
            'wprice.required' => 'Wholesale Price is required',
            'cat_id.required'=> 'Please Choose Category'
        ];
    }
}
