<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|min:4',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Этот email уже используется.',
            'password.required' => 'Поле пароль обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 4 символа.',
        ];
    }
}
