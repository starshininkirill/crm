<?php

namespace App\Http\Requests\Admin;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        if ($this->isMethod('GET')) {
            $rules =  [
                'payment' => 'required|numeric|exists:payments,id'
            ];
        } else if (($this->isMethod('POST') && $this->routeIs('admin.payment.shortlist.attach'))) {
            $rules = [
                'oldPayment' => 'required|numeric|exists:payments,id',
                'newPayment' => 'required|numeric|exists:payments,id'
            ];
        } else if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'value' => 'required|numeric',
                'inn' => 'nullable|numeric',
                'status' => 'required|numeric|in:' . implode(',', Payment::STATUSES),
                'type' => 'required|numeric|in:' . implode(',', Payment::TYPES),
                'is_technical' => 'required|numeric|in:0,1',
                'confirmed_at' => 'nullable|date',
                'created_at' => 'required|date',
                'organization_id' => 'nullable|exists:organizations,id',
                'responsible_id' => 'nullable|exists:users,id',
            ];
        }
        return $rules;
    }
}
