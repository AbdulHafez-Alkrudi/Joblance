<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => 'required',
            'about_task' => 'required',
            'requirements'=> 'required',
            'duration' => 'required',
            'budget_min' => 'required',
            'budget_max' => 'required',
            'major_id' => ['required', 'exists:majors,id']
        ];
    }
}
