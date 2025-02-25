<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:255|min:3',
            'last_name' => 'required|max:255|min:3',
            'email' => 'required|email|unique:users,email',
            // 'position_id' => 'required|exists:positions,id',
            'position_id' => 'exists:positions,id',
            'password' => 'required|min:4',
            'password2' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Поле имя пользователя обязательно для заполнения.',
            'first_name.max' => 'Имя пользователя не должно превышать 255 символов.',
            'first_name.min' => 'Имя пользователя должно содержать минимум 3 символа.',
            'last_name.required' => 'Поле фамилия пользователя обязательно для заполнения.',
            'last_name.max' => 'Фамилия пользователя не должно превышать 255 символов.',
            'last_name.min' => 'Фамилия пользователя должно содержать минимум 3 символа.',
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Этот email уже используется.',
            'password.required' => 'Поле пароль обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password2.required' => 'Поле подтверждение пароля обязательно для заполнения.',
            'password2.same' => 'Пароли не совпадают.',
        ];
    }
}
