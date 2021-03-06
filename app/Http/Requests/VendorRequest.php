<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
            'name' => 'required|string|max:191',
            'email' => 'required',
            'phone' => 'required|regex:/(923)[0-9]{9}/',
            'status'=> 'required|in:0,1'
        ];
    }

    public function attributes()
    {
        return [
            //
            'name' => 'Vendor Name',
            'email' => 'Email Address',
            'phone' => 'Mobile Number',
            'status'=> 'Status'
        ];
    }

    public function messages()
    {
        return [
            //
            'name.required' => 'Vendor Name is required',
            'email.required' => 'Email Address is required',
            'phone.required' => 'Mobile Number is required',
            'status.required'=> 'Please Choose Status'
        ];
    }
}
