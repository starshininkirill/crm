<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
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
            'salary' => 'nullable|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле Название должности обязательно для заполнения.',
            'name.max' => 'Название должности не должно превышать 255 символов.',
            'name.min' => 'Название должности должно содержать минимум 2 символа.',
            'salary.numeric' => 'Зарплата должна быть числом.',
            'salary.min' => 'Зарплата не может быть отрицательной.',
            'department_id.required' => 'Поле Отдел обязательно для заполнения.',
            'department_id.exists' => 'Выбранный отдел не существует.',
        ];
    }
}