<?php

namespace App\Http\Requests\Admin\TimeCheck;

use App\Models\UserAdjustment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UserAdjustmentRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:' . implode(',', UserAdjustment::TYPES),
            'period' => 'required|string|in:' . implode(',', UserAdjustment::PERIODS),
            'value' => 'required|integer|min:1|',
            'description' => 'required|string|min:1',
            'date' => 'required|date',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        $validated['date'] = Carbon::parse($validated['date']);

        return $validated;
    }
}
