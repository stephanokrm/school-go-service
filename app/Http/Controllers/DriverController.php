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
        $roles = Role::query()->where('role', RoleEnum::Driver->value)->pluck('id');

        $user = $this->userService->store($request, collect($request->input('user')));
        $user->roles()->sync($roles);

        $driver = new Driver();
        $driver->fill($request->all());
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
        return new DriverResource($driver->load('user'));
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

        $driver->fill($request->all());
        $driver->user()->associate($user);
        $driver->save();

        return new DriverResource($driver);
    }

    /**
     * @param User $user
     * @param Driver $driver
     * @return DriverResource
     */
    public function destroy(User $user, Driver $driver): DriverResource
    {
        $driver->delete();
        $user->delete();

        return new DriverResource($driver);
    }
}
