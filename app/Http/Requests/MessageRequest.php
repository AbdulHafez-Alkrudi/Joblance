<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
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
    public function rules($request): array
    {
        return [
            'conversation_id' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->input('user_id');
                }),
                'int',
                'exists:conversations,id',
            ],
            'user_id' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->input('conversation_id');
                }),
                'int',
                'exists:users,id',
            ],
            'text' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->hasFile('image') && !$request->hasFile('file');
                }),
                'string'
            ],
            'image' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->post('text') && !$request->hasFile('file');
                }),
                'image',
                'mimes:jpeg,png,bmp,jpg,gif,svg'
            ],
            'file' => [
                Rule::requiredIf(function() use ($request) {
                    return !$request->hasFile('image') && !$request->post('text');
                }),
                'file',
                'mimes:pdf,doc,txt'
            ]
        ];
    }
}
