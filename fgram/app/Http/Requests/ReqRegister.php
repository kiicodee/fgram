<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReqRegister extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required',
            'username' => 'required|min:3|unique:users|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6',
            'bio' => 'required|max:100',
            'is_private' => 'boolean',
        ];
    }
    
}
