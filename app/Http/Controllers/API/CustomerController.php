<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
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
       return Customer::latest()->paginate(5);
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
            'email' => 'required|string|email|max:191|unique:Customers',
            'phone' => 'required|numeric|min:13',
            'status'=> 'required|in:0,1'

        ]);
        return Customer::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'status' => $request['status']
           

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

        $customer = Customer::findOrfail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:Customers,email,' . $customer->id,
            'phone' => 'required|numeric|min:13',
            
            'status'=> 'required|in:0,1'

        ]);
        //update the Customer

        $customer->update($request->all());

        return ['message' => 'Customer Updated'];
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
        $customer = Customer::findOrfail($id);

        //delete the Customer

        $customer->delete();

        return ['message' => 'Customer Deleted'];
    }

    public function search(){

        if ($search = \Request::get('q')) {
            $customers = Customer::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                        ->orWhere('email','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $customers = Customer::latest()->paginate(5);
        }
        return $customers;

    }
}
