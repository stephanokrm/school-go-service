<?php

namespace App\Http\Requests;

class StoreSchoolRequest extends StoreAddressRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        $rules = collect(parent::rules())->mapWithKeys(function (array $rules, string $field) {
            return ["address.{$field}" => $rules];
        });

        return [
            ...$rules,
            'name' => ['required'],
            'morning' => ['sometimes', 'boolean'],
            'afternoon' => ['sometimes', 'boolean'],
            'night' => ['sometimes', 'boolean'],
            'morningEntryTime' => ['sometimes'],
            'morningDepartureTime' => ['sometimes'],
            'afternoonEntryTime' => ['sometimes'],
            'afternoonDepartureTime' => ['sometimes'],
            'nightEntryTime' => ['sometimes'],
            'nightDepartureTime' => ['sometimes'],
        ];
    }
}
