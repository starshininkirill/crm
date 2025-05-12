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
        $rules = [];

        if ($this->isMethod('POST') && $this->routeIs('admin.user.store')) {
            $rules = [
                'first_name' => 'required|max:255|min:3',
                'last_name' => 'required|max:255|min:3',
                'surname' => 'required|max:255|min:3',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:4',
                'work_phone' => 'nullable',
                'bitrix_id' => 'required|numeric',
                'salary' => 'required|numeric',
                'personal_salary' => 'nullable|numeric',
                'probation' => 'nullable|numeric|in:0,1',
                'position_id' => 'required|exists:positions,id',
                'department_id' => 'required|exists:departments,id',
                'employment_type_id' => 'required|exists:employment_types,id',
                'payment_account' => 'required|numeric|digits:20',
                'details' => 'nullable|array',
                'details.*.name' => 'string|required',
                'details.*.readName' => 'string|required',
                'details.*.value' => 'required',
                'probation_dates' => 'nullable|array',
                'probation_dates.*' => 'nullable|date_format:Y-m-d',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [];
    }
}
