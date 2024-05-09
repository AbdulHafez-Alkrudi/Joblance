<?php

namespace App\Http\Requests;

use App\Http\Controllers\BaseController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Gate;

class RegisterCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                => 'required',
            'phone_number'        => 'required|digits:10|unique:users,phone_number',
            'email'               => 'required|ends_with:@gmail.com|unique:users,email',
            'password'            => 'required|min:8',
            'major_id'            => 'required',
            'location'            => 'required',
            'num_of_employees'    => 'required',
            'description'         => 'required',
            'image'               => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return (new BaseController)->sendError($validator->errors());
    }
}
