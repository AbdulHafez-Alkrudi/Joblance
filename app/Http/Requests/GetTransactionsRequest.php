<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTransactionsRequest extends FormRequest
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
            'year'  => ['required', 'integer', 'digits:4', 'between:1900,2099'],
            'month' => ['required', 'integer', 'between:1,12'],
            'day'   => ['nullable', 'integer', 'between:1,31']
        ];
    }
}
