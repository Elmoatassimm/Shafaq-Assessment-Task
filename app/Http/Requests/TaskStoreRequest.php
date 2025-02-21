<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all authenticated users
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        'priority'=> 'nullable|in:low,medium,high'
        ];
    }
}
