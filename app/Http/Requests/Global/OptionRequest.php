<?php

namespace App\Http\Requests\Global;

use Illuminate\Foundation\Http\FormRequest;

class OptionRequest extends FormRequest
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
        if(($this->isMethod('POST') || $this->isMethod('PUT') || $this->isMethod('PACH')) && ($this->routeIs('option.store') || $this->routeIs('option.update'))){
            $rules = [
                'name' => 'required|string|max:255', 
                'value' => 'required' 
            ];
        }else if ($this->isMethod('POST') && $this->routeIs('option.mass-update')){
            $rules = [
                'options' => 'required|array', 
                'options.*.name' => 'required|string|unique:options,name,{id},id,value,{value}',
                'options.*.value' => 'sometimes',
            ];
        }
        return $rules;
    }
}
