<?php

namespace App\Http\Requests;

class UpdateResponsibleRequest extends UpdateUserRequest
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
        $rules = collect(parent::rules())->mapWithKeys(function (array $rules, string $field) {
            return ["user.{$field}" => $rules];
        });

        return [
            ...$rules,
        ];
    }
}
