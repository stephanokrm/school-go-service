<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Itinerary;
use App\Models\Student;
use App\Models\Trip;
use App\Notifications\DisembarkedNotification;
use App\Notifications\EmbarkedNotification;
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
            ->oldest('finished_at')
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
     * @param Trip $trip
     * @return TripResource
     */
    public function show(Trip $trip): TripResource
    {
        return new TripResource($trip->load('students'));
    }

    /**
     * @param UpdateTripRequest $request
     * @param Trip $trip
     * @return TripResource
     */
    public function update(UpdateTripRequest $request, Trip $trip): TripResource
    {
        $trip->fill($request->only($trip->getFillable()));
        $trip->save();

        if ($trip->wasChanged('finished_at')) {
            $trip->getStudents()->each(function (Student $student) {
                $student->getResponsible()->getUser()->notify(new DisembarkedNotification($student));
            });
        }

        return new TripResource($trip);
    }

    /**
     * @param Request $request
     * @param Trip $trip
     * @param Student $student
     * @return TripResource
     */
    public function embark(Request $request, Trip $trip, Student $student): TripResource
    {
        $trip->students()->updateExistingPivot($student->getKey(), [
            'embarked_at' => $request->input('embarked_at'),
        ]);

        $student->getResponsible()->getUser()->notify(new EmbarkedNotification($student));

        return new TripResource($trip);
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

        Trip::query()->whereDate('arrive_at', $today)->delete();

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
                    false
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
                    true
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
                    false
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
                    true
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
                    false
                );
                $this->createTrip(
                    $itinerary,
                    $itinerary->getSchool()->getAttribute('night_departure_time'),
                    $itinerary
                        ->students()
                        ->where('return', true)
                        ->where('night', true)
                        ->get(),
                    true
                );
            }
        });

        return response()->noContent();
    }

    /**
     * @param Itinerary $itinerary
     * @param string $time
     * @param Collection $students
     * @param bool $round
     * @return void
     */
    private function createTrip(
        Itinerary  $itinerary,
        string     $time,
        Collection $students,
        bool       $round
    ): void
    {
        if ($students->isEmpty()) return;

        $arriveAt = Carbon::parse("Today {$time}");
        $origin = $itinerary->getAddress()->getAttribute('place_id');
        $destination = $itinerary->getSchool()->getAddress()->getAttribute('place_id');
        $waypoints = $students->reduce(function (string $waypoints, Student $student) {
            return "{$waypoints}|place_id:{$student->getAddress()->getAttribute('place_id')}";
        }, 'optimize:true');

        $response = \GoogleMaps::load('directions')
            ->setParam([
                'origin' => "place_id:{$origin}",
                'destination' => "place_id:{$destination}",
                'waypoints' => $waypoints,
                'alternatives' => false,
                'driving' => 'driving',
                'arrival_time' => $arriveAt->getTimestamp(),
            ])->get();

        $response = json_decode($response);

        $path = $response->routes[0]->overview_polyline->points;
        $waypointOrder = $response->routes[0]->waypoint_order;

        $orderedStudents = collect($waypointOrder)->reduce(function (Collection $orderedStudents, int $index, int $order) use ($students) {
            return $orderedStudents->put($students->get($index)->getKey(), ['order' => $order + 1]);
        }, collect())->all();

        $trip = new Trip();
        $trip->setAttribute('arrive_at', $arriveAt);
        $trip->setAttribute('path', $path);
        $trip->setAttribute('round', $round);
        $trip->itinerary()->associate($itinerary);
        $trip->save();
        $trip->students()->sync($orderedStudents);
    }
}
