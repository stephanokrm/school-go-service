<?php

namespace App\Services;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserService
{
    /**
     * @param StoreUserRequest $request
     * @param Collection $attributes
     * @return Builder|Model
     */
    public function store(StoreUserRequest $request, Collection $attributes): Builder|Model
    {
        $user = User::query()
            ->where('email', $attributes->get('email'))
            ->where('cell_phone', $attributes->get('cell_phone'))
            ->firstOrNew();
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
