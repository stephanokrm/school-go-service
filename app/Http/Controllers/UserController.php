<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return UserResource
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $user = new User();
        $user->fill($request->all());

        $request->whenFilled('password', function ($password) use ($user) {
            $user->setAttribute('password', Hash::make($password));
        });

        $user->save();

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->fill($request->except('password'));
        $user->save();

        return new UserResource($user);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request): mixed
    {
        return $request->user()->token()->revoke();
    }
}
