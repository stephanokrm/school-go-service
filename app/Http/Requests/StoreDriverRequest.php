<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreDriverRequest extends StoreUserRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return parent::authorize();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = collect(parent::rules())->mapWithKeys(function (array $rules, string $field) {
            return ["user.{$field}" => $rules];
        });

        return [
            ...$rules,
            'license' => ['required', Rule::unique('drivers')],
        ];
    }
}
