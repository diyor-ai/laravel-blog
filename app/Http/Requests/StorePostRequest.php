<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Define user-friendly names for the attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => 'Sarlavha',
            'short_content' => 'Qisqacha mazmun',
            'content' => 'Maq\'ola', // Added missing comma
            'photo' => 'Rasm', // Added 'photo' attribute
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255', // Title is required and must not exceed 255 characters
            'short_content' => 'required', // Short content is required
            'content' => 'required', // Content is required
            'photo' => 'nullable|image|max:2048',
        ];
    }
}