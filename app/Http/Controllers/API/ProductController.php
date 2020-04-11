<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    //
    
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function categories()
    {
        return $this->hasMany('App\Category');
    }

    public function index()
    {
       // $this->authorize('isAdmin');
       if (Gate::allows('isAdmin') || Gate::allows('isAuthor')) {
       return Product::join('categories','categories.id','=', 'products.cat_id')
       ->select('products.id',
       'products.name as name',
       'products.pprice as pprice',
       'products.sprice as sprice',
       'products.wprice as wprice',
       'categories.id as cat_id',
       'categories.name as cat_name'
    )->paginate(5);
       }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        
        $this->validate($request, [
            'name' => 'required|string|max:191|unique:products',
         ]);
        return Product::create([
            'name' => $request['name'],
            'cat_id' => $request['cat_id'],
            'pprice' => $request['pprice'],
            'sprice' => $request['sprice'],
            'wprice' => $request['wprice']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        //

        $product = Product::findOrfail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191|unique:products,name,' .$product->id,
         ]);
        //update the product

        $product->update($request->all());

        return ['message' => 'Product Updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $this->authorize('isAdmin');
        $product = Product::findOrfail($id);

        //delete the product

        $product->delete();

        return ['message' => 'product Deleted'];
    }

    public function search(){

        if ($search = \Request::get('q')) {
            $products = Product::join('categories','categories.id','=', 'products.cat_id')
            ->select('products.id',
            'products.name as name',
            'products.pprice as pprice',
            'products.sprice as sprice',
            'products.wprice as wprice',
            'categories.id as cat_id',
            'categories.name as cat_name'
         )->where(function($query) use ($search){
                $query->where('products.name','LIKE',"%$search%")
                        ->orWhere('products.pprice','LIKE',"%$search%")->orWhere('categories.name','LIKE',"%$search%");
                    })->paginate(5);
        }else{
            $products = Product::join('categories','categories.id','=', 'products.cat_id')
            ->select('products.id',
            'products.name as name',
            'products.pprice as pprice',
            'products.sprice as sprice',
            'products.wprice as wprice',
            'categories.id as cat_id',
            'categories.name as cat_name'
         )->paginate(5);
        }

        return $products;

    }
}
