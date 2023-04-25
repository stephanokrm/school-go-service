<?php

namespace App\Http\Controllers;

use App\Enums\Role as RoleEnum;
use App\Http\Requests\StoreResponsibleRequest;
use App\Http\Requests\UpdateResponsibleRequest;
use App\Http\Resources\ResponsibleResource;
use App\Models\Responsible;
use App\Models\Role;
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
        $roles = Role::query()->where('role', RoleEnum::Responsible->value)->pluck('id');

        $user = $this->userService->store($request, collect($request->input('user')));
        $user->roles()->sync($roles);

        $responsible = new Responsible();
        $responsible->fill($request->except('user'));
        $responsible->user()->associate($user);
        $responsible->save();

        return new ResponsibleResource($responsible);
    }

    /**
     * Display the specified resource.
     */
    public function show(Responsible $responsible)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResponsibleRequest $request, Responsible $responsible)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Responsible $responsible)
    {
        //
    }
}
