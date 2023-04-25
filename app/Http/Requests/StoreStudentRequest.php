<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreStudentRequest extends StoreAddressRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        $rules = collect(parent::rules())->mapWithKeys(function (array $rules, string $field) {
            return ["address.{$field}" => $rules];
        });

        return [
            ...$rules,
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'goes' => ['sometimes', 'boolean'],
            'return' => ['sometimes', 'boolean'],
            'morning' => ['sometimes', 'boolean'],
            'afternoon' => ['sometimes', 'boolean'],
            'night' => ['sometimes', 'boolean'],
            'responsible.id' => ['required', 'integer', Rule::exists('responsibles', 'id')],
            'school.id' => ['required', 'integer', Rule::exists('schools', 'id')],
        ];
    }
}
