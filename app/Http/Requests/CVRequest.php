<?php

namespace App\Http\Requests;

use Google\Rpc\Context\AttributeContext\Request;
use Illuminate\Foundation\Http\FormRequest;

class CVRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'major' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'link' => 'nullable|url|max:255',
            'summary' => 'required|string',
            'skills' => 'required|array',
            'certificates' => 'required|array',
            'educations' => 'required|array',
            'experiences' => 'required|array',
            'country' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'profile_image' => 'nullable|image|max:2048'
        ];
    }
}
