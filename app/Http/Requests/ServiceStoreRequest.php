<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStoreRequest extends FormRequest
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
            'name' => 'required|min:2|max:255|unique:services,name',
            'description' => 'nullable|min:2',
            'work_days_duration' => 'nullable|min:1',
            'price' => 'required|numeric',
            'service_category_id' => 'required|exists:service_categories,id',
            'deal_template_ids' => 'nullable|json',
            'law_default' => 'nullable|numeric|required_with:law_complex,physic_default,physic_complex',
            'law_complex' => 'nullable|numeric|required_with:law_default,physic_default,physic_complex',
            'physic_default' => 'nullable|numeric|required_with:law_default,law_complex,physic_complex',
            'physic_complex' => 'nullable|numeric|required_with:law_default,physic_default,law_complex',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле Название услуги пользователя обязательно для заполнения.',
            'name.max' => 'Название услуги не должно превышать 255 символов.',
            'name.min' => 'Название услуги должно содержать минимум 3 символа.',
            'service_category_id.required' => 'Выберите категорию для услуги.',
            'service_category_id.exists' => 'Выбранная категория не существует.',
        ];
    }
}
