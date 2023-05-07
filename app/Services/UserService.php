<?php

namespace App\Services;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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
        }, function () use ($user) {
            if ($user->getAttribute('password') === null) {
                $user->setAttribute('password', Hash::make('password'));
            }
        });

        $user->save();

        Password::sendResetLink([
            'email' => $user->getAttribute('email'),
        ]);

        return $user;
    }

    /**
     * @param User $user
     * @param Collection $attributes
     * @return User
     */
    public function update(User $user, Collection $attributes): User
    {
        $user->fill($attributes->only($user->getFillable())->all());
        $user->save();

        return $user;
    }
}
