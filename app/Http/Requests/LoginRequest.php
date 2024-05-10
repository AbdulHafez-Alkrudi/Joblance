<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'ends_with:@gmail.com', 'exists:users,email'],
            'password' => 'required',
            'device_token' => [
                Rule::requiredIf(function() use ($request){
                    //return Gate::allows('isAdmin', User::query()->find(1)->toArray());
                     return !$request->input('isAdmin');
                }),
                'string',
            ]
        ];
    }
}
