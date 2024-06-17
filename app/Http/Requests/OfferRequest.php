<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
            'task_id'    => ['required', 'exists:tasks,id'],
            'first_name' => ['required', 'string'],
            'last_name'  => ['required', 'string'],
            'email'      => ['required', 'email', 'ends_with:@gmail.com'],
            'phone'      => ['required', 'digits:10'],
            'budget'     => ['required', 'min:0', 'max:999999'],
            'years_of_experience' => ['required', 'integer'],
            'excuting_time' => ['required', 'integer'],
            'offer_information' => ['nullable', 'string'],
        ];
    }
}
