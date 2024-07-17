<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobDetailRequest extends FormRequest
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
            'job_type_id' => ['required', 'exists:job_types,id'],
            'experience_level_id' => ['required', 'exists:experience_levels,id'],
            'remote_id' => ['required', 'exists:remotes,id'],
            'major_id'  => ['required', 'exists:majors,id'],
            'title' => ['required', 'string'],
            'location' => ['required', 'string'],
            'job_description' => ['required', 'string'],
            'requirements' => ['required', 'string'],
            'additional_information' => ['nullable', 'string'],
            'salary' => ['required']
        ];
    }
}
