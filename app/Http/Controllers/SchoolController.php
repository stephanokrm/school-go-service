<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Models\Address;
use App\Models\School;

class SchoolController extends Controller
{
    /**
     * @return SchoolResource
     */
    public function index(): SchoolResource
    {
        return new SchoolResource(School::with('address')->get());
    }

    /**
     * @param StoreSchoolRequest $request
     * @return SchoolResource
     */
    public function store(StoreSchoolRequest $request): SchoolResource
    {
        $address = new Address();
        $address->fill($request->input('address'));
        $address->save();

        $school = new School();
        $school->fill($request->all());
        $school->address()->associate($address);
        $school->save();

        return new SchoolResource($school);
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        //
    }
}
