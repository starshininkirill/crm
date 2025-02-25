<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'description' => 'nullable|min:2',
            'work_days_duration' => 'nullable|min:1',
            'price' => 'required|numeric',
            'service_category_id' => 'required|exists:service_categories,id',
        ];

        if($this->isMethod('POST')){
            $rules = array_merge($rules, [
                'name' => 'required|min:2|max:255|unique:services,name',
            ]);
        }

        if($this->isMethod('PUT') || $this->isMethod('PATCH')){
            $service = $this->route('service');

            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    'min:2',
                    'max:255',
                    Rule::unique('services', 'name')->ignore($service->id), 
                ],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле Название услуги пользователя обязательно для заполнения.',
            'name.max' => 'Название услуги не должно превышать 255 символов.',
            'name.min' => 'Название услуги должно содержать минимум 2 символа.',
            'service_category_id.required' => 'Выберите категорию для услуги.',
            'service_category_id.exists' => 'Выбранная категория не существует.',
        ];
    }
}
