<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле Название отдела пользователя обязательно для заполнения.',
            'name.max' => 'Название отдела не должно превышать 255 символов.',
            'name.min' => 'Название отдела должно содержать минимум 3 символа.',
        ];
    }
}
