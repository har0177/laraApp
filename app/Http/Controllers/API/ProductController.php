<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
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
    public function index()
    {
       // $this->authorize('isAdmin');
       if (Gate::allows('isAdmin') || Gate::allows('isAuthor')) {
       return Product::latest()->paginate(5);
       }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191|unique:products',
            'pprice' => 'required|numeric',
            'sprice' => 'required|numeric',
            'wprice' => 'required|numeric'

        ]);
        return Product::create([
            'name' => $request['name'],
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
    public function update(Request $request, $id)
    {
        //

        $product = Product::findOrfail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191|unique:products',
            'pprice' => 'required|numeric',
            'sprice' => 'required|numeric',
            'wprice' => 'required|numeric'

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
            $products = Product::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                        ->orWhere('pprice','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $products = Product::latest()->paginate(5);
        }

        return $products;

    }
}
