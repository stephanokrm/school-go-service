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
        return [
            'user.first_name' => ['required', 'max:255'],
            'user.last_name' => ['required', 'max:255'],
            'user.email' => ['required', 'email', 'max:255'],
            'user.cell_phone' => ['required', Rule::phone()->country(['BR'])->type('mobile')],
            'license' => ['required', Rule::unique('drivers')],
        ];
    }
}
