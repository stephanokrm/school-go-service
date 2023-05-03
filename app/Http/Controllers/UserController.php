<?php

namespace App\Http\Controllers;

use App\Enums\Role as RoleEnum;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    /**
     * @return UserResource
     */
    public function index(): UserResource
    {
        return new UserResource(User::query()->orderBy('first_name')->orderBy('last_name')->get());
    }

    /**
     * @param Request $request
     * @return UserResource
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $roles = Role::query()->where('role', RoleEnum::Administrator->value)->pluck('id');

        $user = $this->userService->store($request, collect($request->all()));
        $user->roles()->sync($roles);

        Password::sendResetLink([
            'email' => $user->getAttribute('email'),
        ]);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user = $this->userService->update($user, collect($request->all()));

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

    /**
     * @param Request $request
     * @return bool
     */
    public function email(Request $request): bool
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return User::query()->where('email', $request->query('email'))->exists();
    }
}
