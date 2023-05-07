<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Itinerary;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TripController extends Controller
{
    /**
     * @param Itinerary $itinerary
     * @param string $time
     * @return void
     */
    private function createTrip(
        Itinerary $itinerary,
        string    $time
    )
    {
        $trip = new Trip();
        $trip->setAttribute('arrive_at', Carbon::parse("Today {$time}")->getTimestamp());
        $trip->itinerary()->associate($itinerary);
        $trip->save();
    }

    /**
     * @param Request $request
     * @return TripResource
     */
    public function index(Request $request): TripResource
    {
        $today = Carbon::today();

        $itineraries = Itinerary::query()
            ->when($today->isMonday(), function (Builder $builder) {
                $builder->orWhere('monday', true);
            })
            ->when($today->isTuesday(), function (Builder $builder) {
                $builder->orWhere('tuesday', true);
            })
            ->when($today->isWednesday(), function (Builder $builder) {
                $builder->orWhere('wednesday', true);
            })
            ->when($today->isThursday(), function (Builder $builder) {
                $builder->orWhere('thursday', true);
            })
            ->when($today->isFriday(), function (Builder $builder) {
                $builder->orWhere('friday', true);
            })
            ->when($request->query('driver', false), function (Builder $builder) use ($request) {
                $builder->where('driver_id', $request->user()->driver->id);
            })
            ->get();

        $itineraries->each(function (Itinerary $itinerary) {
            if ($itinerary->getAttribute('morning')) {
                $this->createTrip($itinerary, $itinerary->getSchool()->getAttribute('morning_entry_time'));
                $this->createTrip($itinerary, $itinerary->getSchool()->getAttribute('morning_departure_time'));
            }

            if ($itinerary->getAttribute('afternoon')) {
                $this->createTrip($itinerary, $itinerary->getSchool()->getAttribute('afternoon_entry_time'));
                $this->createTrip($itinerary, $itinerary->getSchool()->getAttribute('afternoon_departure_time'));
            }

            if ($itinerary->getAttribute('night')) {
                $this->createTrip($itinerary, $itinerary->getSchool()->getAttribute('night_entry_time'));
                $this->createTrip($itinerary, $itinerary->getSchool()->getAttribute('night_departure_time'));
            }
        });

        return new TripResource([]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        //
    }
}
