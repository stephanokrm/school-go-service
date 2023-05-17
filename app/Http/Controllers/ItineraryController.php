<?php

namespace App\Http\Controllers;

use App\Events\TripUpdated;
use App\Http\Requests\StoreItineraryRequest;
use App\Http\Requests\UpdateItineraryRequest;
use App\Http\Resources\ItineraryResource;
use App\Models\Driver;
use App\Models\Itinerary;
use App\Models\School;
use App\Models\Trip;
use App\Services\AddressService;
use Illuminate\Support\Collection;

class ItineraryController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService
    )
    {
    }

    /**
     * @return ItineraryResource
     */
    public function index(): ItineraryResource
    {
        return new ItineraryResource(Itinerary::all());
    }

    /**
     * @param StoreItineraryRequest $request
     * @return ItineraryResource
     */
    public function store(StoreItineraryRequest $request): ItineraryResource
    {
        $driver = Driver::query()->findOrFail($request->input('driver.id'));
        $school = School::query()->findOrFail($request->input('school.id'));

        $address = $this->addressService->store(collect($request->input('address')));

        $itinerary = new Itinerary();
        $itinerary->fill($request->only($itinerary->getFillable()));
        $itinerary->address()->associate($address);
        $itinerary->driver()->associate($driver);
        $itinerary->school()->associate($school);
        $itinerary->save();
        $itinerary->students()->sync(collect($request->input('students'))->pluck('id'));

        return new ItineraryResource($itinerary);
    }

    /**
     * @param Itinerary $itinerary
     * @return ItineraryResource
     */
    public function show(Itinerary $itinerary): ItineraryResource
    {
        return new ItineraryResource($itinerary->load('students'));
    }

    /**
     * @param UpdateItineraryRequest $request
     * @param Itinerary $itinerary
     * @return ItineraryResource
     */
    public function update(UpdateItineraryRequest $request, Itinerary $itinerary): ItineraryResource
    {
        $driver = Driver::query()->findOrFail($request->input('driver.id'));
        $school = School::query()->findOrFail($request->input('school.id'));

        $this->addressService->update($itinerary->getAddress(), collect($request->input('address')));

        $itinerary->fill($request->only($itinerary->getFillable()));
        $itinerary->driver()->associate($driver);
        $itinerary->school()->associate($school);
        $itinerary->save();

        $changes = $itinerary->students()->sync(collect($request->input('students'))->pluck('id'));

        $attached = collect($changes['attached']);
        $detached = collect($changes['detached']);

        if ($attached->isNotEmpty() || $detached->isNotEmpty()) {
            $itinerary
                ->trips()
                ->whereNull('finished_at')
                ->get()
                ->when($attached->isNotEmpty(), function (Collection $trips) use ($attached) {
                    $trips->each(function (Trip $trip) use ($attached) {
                        $trip->students()->attach($attached);
                    });
                })
                ->when($detached->isNotEmpty(), function (Collection $trips) use ($detached) {
                    $trips->each(function (Trip $trip) use ($detached) {
                        $trip->students()->detach($detached);
                    });
                })
                ->each(function (Trip $trip) {
                    event(new TripUpdated($trip));
                });
        }

        return new ItineraryResource($itinerary);
    }

    /**
     * @param Itinerary $itinerary
     * @return bool|null
     */
    public function destroy(Itinerary $itinerary): ?bool
    {
        return $itinerary->delete();
    }
}
