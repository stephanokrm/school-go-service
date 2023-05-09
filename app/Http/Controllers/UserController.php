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

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user = $this->userService->update($user, collect($request->all()));

        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return bool|null
     */
    public function destroy(User $user): ?bool
    {
        return $user->delete();
    }
}
