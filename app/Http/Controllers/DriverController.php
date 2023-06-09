<?php

namespace App\Http\Controllers;

use App\Enums\Role as RoleEnum;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;

class DriverController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    /**
     * @return DriverResource
     */
    public function index(): DriverResource
    {
        return new DriverResource(Driver::with('user')->get());
    }

    /**
     * @param StoreDriverRequest $request
     * @return DriverResource
     */
    public function store(StoreDriverRequest $request): DriverResource
    {
        $role = Role::query()->where('role', RoleEnum::Driver->value)->first();

        $user = $this->userService->store($request, collect($request->input('user')));
        $user->roles()->attach($role->getKey());

        $driver = new Driver();
        $driver->fill($request->only($driver->getFillable()));
        $driver->user()->associate($user);
        $driver->save();

        return new DriverResource($driver);
    }

    /**
     * @param Driver $driver
     * @return DriverResource
     */
    public function show(Driver $driver): DriverResource
    {
        return new DriverResource($driver);
    }

    /**
     * @param UpdateDriverRequest $request
     * @param User $user
     * @param Driver $driver
     * @return DriverResource
     */
    public function update(UpdateDriverRequest $request, User $user, Driver $driver): DriverResource
    {
        $user = $this->userService->update($user, collect($request->input('user')));

        $driver->fill($request->only($driver->getFillable()));
        $driver->user()->associate($user);
        $driver->save();

        return new DriverResource($driver);
    }
}
