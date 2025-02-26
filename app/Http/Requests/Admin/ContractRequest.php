<?php

namespace App\Http\Requests\Admin;

use App\Models\ContractUser;
use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
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
        $rules = [];

        if ($this->isMethod('POST') && $this->routeIs('admin.contract.attach-performer')) {
            $allowedRoles = ContractUser::getRoles()->toArray();

            $rules = array_merge($rules, [
                'performersData' => 'required|array',
                'performersData.*.id' => 'required|integer|in:' . implode(',', $allowedRoles),
                'performersData.*.performers' => 'nullable|array',
                'performersData.*.performers.*' => 'nullable|exists:users,id',
            ]);
        }

        return $rules;
    }

    public function getPerformersData()
    {
        return $this->get('performersData');
    }
}
