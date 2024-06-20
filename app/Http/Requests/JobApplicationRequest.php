<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicationRequest extends FormRequest
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
            'job_detail_id' => ['required', 'exists:job_details,id'],
            'first_name'    => ['required', 'string'],
            'last_name'     => ['required', 'string'],
            'email'         => ['required', 'email', 'ends_with:@gmail.com'],
            'phone_number'  => ['required', 'digits:10'],
            'CV'            => ['required', 'mimes:pdf'],
            'cover_letter'  => ['nullable', 'string']
        ];
    }
}
