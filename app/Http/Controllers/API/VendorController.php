<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class VendorController extends Controller
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
       return Vendor::latest()->paginate(5);
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
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:Vendors',
            'phone' => 'required|numeric|min:13'

        ]);
        return Vendor::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address']
           

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

        $vendor = Vendor::findOrfail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:Vendors,email,' . $vendor->id,
            'phone' => 'required|numeric|min:13'

        ]);
        //update the Vendor

        $vendor->update($request->all());

        return ['message' => 'Vendor Updated'];
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
        $vendor = Vendor::findOrfail($id);

        //delete the Vendor

        $vendor->delete();

        return ['message' => 'Vendor Deleted'];
    }

    public function search(){

        if ($search = \Request::get('q')) {
            $vendors = Vendor::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                        ->orWhere('email','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $vendors = Vendor::latest()->paginate(5);
        }
        return $vendors;

    }
}
