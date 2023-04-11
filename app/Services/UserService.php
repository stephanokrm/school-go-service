<?php

namespace App\Services;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @param StoreUserRequest $request
     * @param Collection $attributes
     * @return User
     */
    public function store(StoreUserRequest $request, Collection $attributes): User
    {
        $user = new User();
        $user->fill($attributes->all());

        $request->whenFilled('password', function ($password) use ($user) {
            $user->setAttribute('password', Hash::make($password));
        });

        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @param Collection $attributes
     * @return User
     */
    public function update(User $user, Collection $attributes): User
    {
        $user->fill($attributes->except('password')->all());
        $user->save();

        return $user;
    }
}
