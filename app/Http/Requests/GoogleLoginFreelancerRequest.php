<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginFreelancerRequest extends FormRequest
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
            'first_name'    => 'required',
            'last_name'     => 'required',
            'phone_number'  => ['required', 'digits:10', 'unique:users,phone_number'],
            'email'         => ['required', 'ends_with:@gmail.com', 'exists:users,email'],
            'major_id'      => 'required',
            'location'      => 'required',
            'study_case_id' => 'required',
            'open_to_work'  => 'required',
            'birth_date'    => 'required',
            'bio'           => 'required',
            'gender'        => 'required',
            'image'         => 'required',
        ];
    }
}
