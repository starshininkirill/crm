<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceCategoryRequest extends FormRequest
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
        if($this->isMethod('POST')){
            return [
                'name' => 'required|min:2|max:255|unique:service_categories,name',
                'type' => 'nullable|numeric|unique:service_categories,type'
            ];
    
        }

        if($this->isMethod('PUT') || $this->isMethod('PATCH')){
            
            $serviceCategory = $this->route('serviceCategory');

            return [
                'name' => [
                    'required',
                    'min:2',
                    'max:255',
                    Rule::unique('service_categories', 'name')->ignore($serviceCategory->id), 
                ],
                'type' => [
                    'nullable',
                    'numeric',
                    Rule::unique('service_categories', 'type')->ignore($serviceCategory->id), 
                ],
            ];
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле Категория пользователя обязательно для заполнения.',
            'name.max' => 'Категория не должно превышать 255 символов.',
            'name.min' => 'Категория должно содержать минимум 2 символа.',
        ];
    }
}