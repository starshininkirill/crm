<?php

namespace App\Http\Requests\Lk;

use Illuminate\Foundation\Http\FormRequest;

class OverworkRequest extends FormRequest
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
            'hours' => 'required|integer',
            'date' => 'required|date|date_format:Y-m-d',
            'report' => 'nullable',
            'links' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $links = explode(',', $value);
                    foreach ($links as $link) {
                        $link = trim($link);
                        if (!preg_match('/^https:\/\/grampus\.bitrix24\.ru\/.*/', $link)) {
                            $fail("Поле $attribute содержит некорректную ссылку: $link");
                        }
                    }
                },
            ],
        ];
    }
}
