<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class FinanceWeekRequest extends FormRequest
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
            'week' => 'array|min:1|max:5',
            'week.*' => 'array', 
            'week.*.date_start' => 'nullable|date|before_or_equal:week.*.date_end', 
            'week.*.date_end' => 'nullable|date|after_or_equal:week.*.date_start',  
            'week.*.weeknum' => 'required|integer|min:1|max:5',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['week']) && is_array($validated['week'])) {
            $validated['week'] = array_filter($validated['week'], function ($week) {
                return !is_null($week['date_start']) && !is_null($week['date_end']);
            });
        }

        foreach($validated['week'] as $key => $week){
            if($key <= 1){
                continue;
            }

            if($validated['week'][$key - 1]['date_end'] >= $week['date_start'] ){
                $updatedStartDaye = Carbon::parse($validated['week'][$key - 1]['date_end'])->addDay()->format('Y-m-d'); 
                $validated['week'][$key]['date_start'] =  $updatedStartDaye;
            }

        }

        return $validated;
    }
}
