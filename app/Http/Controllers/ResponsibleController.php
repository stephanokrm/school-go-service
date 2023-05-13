<?php

namespace App\Http\Controllers;

use App\Enums\Role as RoleEnum;
use App\Http\Requests\StoreResponsibleRequest;
use App\Http\Requests\UpdateResponsibleRequest;
use App\Http\Resources\ResponsibleResource;
use App\Models\Responsible;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;

class ResponsibleController extends Controller
{
    /**
     * @param UserService $userService
     */
    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    /**
     * @return ResponsibleResource
     */
    public function index(): ResponsibleResource
    {
        return new ResponsibleResource(Responsible::all());
    }

    /**
     * @param StoreResponsibleRequest $request
     * @return ResponsibleResource
     */
    public function store(StoreResponsibleRequest $request): ResponsibleResource
    {
        $role = Role::query()->where('role', RoleEnum::Responsible->value)->first();

        $user = $this->userService->store($request, collect($request->input('user')));
        $user->roles()->attach($role->getKey());

        $responsible = new Responsible();
        $responsible->fill($request->except('user'));
        $responsible->user()->associate($user);
        $responsible->save();

        return new ResponsibleResource($responsible);
    }

    /**
     * @param Responsible $responsible
     * @return ResponsibleResource
     */
    public function show(Responsible $responsible): ResponsibleResource
    {
        return new ResponsibleResource($responsible);
    }

    /**
     * @param UpdateResponsibleRequest $request
     * @param User $user
     * @param Responsible $responsible
     * @return ResponsibleResource
     */
    public function update(UpdateResponsibleRequest $request, User $user, Responsible $responsible): ResponsibleResource
    {
        $user = $this->userService->update($user, collect($request->input('user')));

        $responsible->user()->associate($user);
        $responsible->save();

        return new ResponsibleResource($responsible);
    }
}
