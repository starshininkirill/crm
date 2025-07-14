<?php

namespace App\Http\Requests\Admin\Staff;

use App\Models\Note;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class NoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required|date_format:Y-m',
            'content' => 'required|string',
        ];
    }
} 