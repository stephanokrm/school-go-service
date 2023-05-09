<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Itinerary;
use App\Models\Student;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TripController extends Controller
{
    /**
     * @param Request $request
     * @return TripResource
     */
    public function index(Request $request): TripResource
    {
        $trips = Trip::query()
            ->when($request->query('driver', false), function (Builder $builder) use ($request) {
                $builder->whereRelation('itinerary', 'driver_id', $request->user()->driver->id);
            })
            ->whereDate('arrive_at', Carbon::today())
            ->oldest('arrive_at')
            ->get();

        return new TripResource($trips);
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
    public function show(Request $request, Trip $trip)
    {
        $origin = $trip->getItinerary()->getSchool()->getAddress()->getAttribute('place_id');

        $waypoints = $trip->students()->get()->reduce(function (string $waypoints, Student $student) {
            return "{$waypoints}|place_id:{$student->getAddress()->getAttribute('place_id')}";
        }, 'optimize:true');

        return \GoogleMaps::load('directions')
            ->setParam([
                'origin' => 'place_id:ChIJ685WIFYViEgRHlHvBbiD5nE',
                'destination' => "place_id:{$origin}",
                'waypoints' => $waypoints,
                'provideRouteAlternatives' => false,
                'travelMode' => 'DRIVING',
            ])->get();
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

    /**
     * @return Response
     */
    public function schedule(): Response
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
            ->get();

        $itineraries->each(function (Itinerary $itinerary) {
            if ($itinerary->getSchool()->getAttribute('morning')) {
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('morning_entry_time'),
                    $itinerary
                        ->students()
                        ->where('goes', true)
                        ->where('morning', true)
                        ->get(),
                );
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('morning_departure_time'),
                    $itinerary
                        ->students()
                        ->where('return', true)
                        ->where('morning', true)
                        ->where('afternoon', false)
                        ->get(),
                );
            }

            if ($itinerary->getSchool()->getAttribute('afternoon')) {
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('afternoon_entry_time'),
                    $itinerary
                        ->students()
                        ->where('goes', true)
                        ->where('morning', false)
                        ->where('afternoon', true)
                        ->get(),
                );
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('afternoon_departure_time'),
                    $itinerary
                        ->students()
                        ->where('return', true)
                        ->where('afternoon', true)
                        ->where('night', false)
                        ->get(),
                );
            }

            if ($itinerary->getSchool()->getAttribute('night')) {
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('night_entry_time'),
                    $itinerary
                        ->students()
                        ->where('goes', true)
                        ->where('afternoon', false)
                        ->where('night', true)
                        ->get(),
                );
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('night_departure_time'),
                    $itinerary
                        ->students()
                        ->where('return', true)
                        ->where('night', true)
                        ->get(),
                );
            }
        });

        return response()->noContent();
    }

    /**
     * @param Itinerary $itinerary
     * @param string $time
     * @param Collection $students
     * @return void
     */
    private function createTrip(
        Itinerary  $itinerary,
        string     $time,
        Collection $students,
    ): void
    {
        $arriveAt = Carbon::parse("Today {$time}");

        $trip = Trip::query()
            ->whereDate('arrive_at', $arriveAt)
            ->where('itinerary_id', $itinerary->getKey())
            ->firstOrNew();

        $trip->setAttribute('arrive_at', $arriveAt);
        $trip->itinerary()->associate($itinerary);
        $trip->save();
        $trip->students()->sync($students->pluck('id'));
    }
}
