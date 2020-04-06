<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
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
       return Category::latest()->paginate(5);
       }
    }

    public function categories()
    {
       // $this->authorize('isAdmin');
       return Category::all();
       
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
            'name' => 'required|string|max:191|unique:categories'
           
        ]);
        return Category::create([
            'name' => $request['name']
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

        $category = Category::findOrfail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191|unique:categories'
           

        ]);
        //update the category

        $category->update($request->all());

        return ['message' => 'Category Updated'];
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
        $category = Category::findOrfail($id);

        //delete the category

        $category->delete();

        return ['message' => 'Category Deleted'];
    }

    public function search(){

        if ($search = \Request::get('q')) {
            $category = Category::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $category = Category::latest()->paginate(5);
        }

        return $category;

    }
}
