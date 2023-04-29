<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItineraryRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'monday' => ['sometimes', 'boolean'],
            'tuesday' => ['sometimes', 'boolean'],
            'wednesday' => ['sometimes', 'boolean'],
            'thursday' => ['sometimes', 'boolean'],
            'friday' => ['sometimes', 'boolean'],
            'morning' => ['sometimes', 'boolean'],
            'afternoon' => ['sometimes', 'boolean'],
            'night' => ['sometimes', 'boolean'],
            'driver.id' => ['required', 'integer', Rule::exists('drivers', 'id')],
            'school.id' => ['required', 'integer', Rule::exists('schools', 'id')],
            'students.*.id' => ['required', 'integer', Rule::exists('students', 'id')],
        ];
    }
}
