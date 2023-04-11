<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateDriverRequest extends UpdateUserRequest
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
        $driver = $this->route('driver');

        $rules = collect(parent::rules())->mapWithKeys(function (array $rules, string $field) {
            return ["user.{$field}" => $rules];
        });

        return [
            ...$rules,
            'license' => ['required', Rule::unique('drivers')->ignoreModel($driver)],
        ];
    }
}
