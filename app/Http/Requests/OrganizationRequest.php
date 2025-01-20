<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
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
        $rules = [
            'short_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'inn' => 'required|unique:organizations,inn',
            'nds' => 'required|integer|in:0,1',
            'terminal' => 'required|integer|min:1',
        ];

        // if ($this->isMethod('POST')) {

        // }

        // if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
        //     $service = $this->route('service');

        //     $rules = array_merge($rules, [
        //         'name' => [
        //             'required',
        //             'min:2',
        //             'max:255',
        //             Rule::unique('services', 'name')->ignore($service->id),
        //         ],
        //     ]);
        // }

        return $rules;
    }
}
