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
        return new SchoolResource(School::query()->orderBy('name')->get());
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

        $request->whenFilled('morning', function ($morning) use ($request, $school) {
            if (!$morning) return;

            $school->setAttribute('morning_entry_time', $request->date('morning_entry_time')->format('H:i'));
            $school->setAttribute('morning_departure_time', $request->date('morning_departure_time')->format('H:i'));
        });

        $request->whenFilled('afternoon', function ($afternoon) use ($request, $school) {
            if (!$afternoon) return;

            $school->setAttribute('afternoon_entry_time', $request->date('afternoon_entry_time')->format('H:i'));
            $school->setAttribute('afternoon_departure_time', $request->date('afternoon_departure_time')->format('H:i'));
        });

        $request->whenFilled('night', function ($night) use ($request, $school) {
            if (!$night) return;

            $school->setAttribute('night_entry_time', $request->date('night_entry_time')->format('H:i'));
            $school->setAttribute('night_departure_time', $request->date('night_departure_time')->format('H:i'));
        });

        $school->address()->associate($address);
        $school->save();

        return new SchoolResource($school);
    }

    /**
     * @param School $school
     * @return SchoolResource
     */
    public function show(School $school): SchoolResource
    {
        return new SchoolResource($school->load('address'));
    }

    /**
     * @param UpdateSchoolRequest $request
     * @param School $school
     * @return SchoolResource
     */
    public function update(UpdateSchoolRequest $request, School $school): SchoolResource
    {
        $school->address->fill($request->input('address'));
        $school->address->save();
        $school->fill($request->except('address'));

        $request->whenFilled('morning', function ($morning) use ($request, $school) {
            if ($morning) {
                $school->setAttribute('morning_entry_time', $request->date('morning_entry_time')->format('H:i'));
                $school->setAttribute('morning_departure_time', $request->date('morning_departure_time')->format('H:i'));
            } else {
                $school->setAttribute('morning_entry_time', null);
                $school->setAttribute('morning_departure_time', null);
            }
        });

        $request->whenFilled('afternoon', function ($afternoon) use ($request, $school) {
            if ($afternoon) {
                $school->setAttribute('afternoon_entry_time', $request->date('afternoon_entry_time')->format('H:i'));
                $school->setAttribute('afternoon_departure_time', $request->date('afternoon_departure_time')->format('H:i'));
            } else {
                $school->setAttribute('afternoon_entry_time', null);
                $school->setAttribute('afternoon_departure_time', null);
            }
        });

        $request->whenFilled('night', function ($night) use ($request, $school) {
            if ($night) {
                $school->setAttribute('night_entry_time', $request->date('night_entry_time')->format('H:i'));
                $school->setAttribute('night_departure_time', $request->date('night_departure_time')->format('H:i'));
            } else {
                $school->setAttribute('night_entry_time', null);
                $school->setAttribute('night_departure_time', null);
            }
        });

        $school->save();

        return new SchoolResource($school);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        //
    }
}
