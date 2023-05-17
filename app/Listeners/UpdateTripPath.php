<?php

namespace App\Listeners;

use App\Events\TripUpdated;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UpdateTripPath
{
    /**
     * @param TripUpdated $event
     * @return void
     */
    public function handle(TripUpdated $event): void
    {
        $trip = $event->getTrip();
        $newOriginPlaceId = $event->getOrigin();

        $itineraryAddressPlaceId = $trip->getItinerary()->getAddress()->getAttribute('place_id');
        $schoolAddressPlaceId = $trip->getItinerary()->getSchool()->getAddress()->getAttribute('place_id');

        if ($newOriginPlaceId) {
            $originPlaceId = $newOriginPlaceId;
        } else {
            $originPlaceId = $trip->getAttribute('round') ? $schoolAddressPlaceId : $itineraryAddressPlaceId;
        }

        $destinationPlaceId = $trip->getAttribute('round') ? $itineraryAddressPlaceId : $schoolAddressPlaceId;
        $students = $trip
            ->students()
            ->when($trip->getAttribute('round'), function (Builder $query) {
                $query->whereNull('student_trip.disembarked_at');
            })
            ->when(!$trip->getAttribute('round'), function (Builder $query) {
                $query->whereNull('student_trip.embarked_at');
            })
            ->get();

        $waypoints = $students->isEmpty() ? null : $students->reduce(function (string $waypoints, Student $student) {
            return "{$waypoints}|place_id:{$student->getAddress()->getAttribute('place_id')}";
        }, 'optimize:true');

        $params = [
            'origin' => "place_id:{$originPlaceId}",
            'destination' => "place_id:{$destinationPlaceId}",
            'waypoints' => $waypoints,
            'alternatives' => false,
            'mode' => 'driving',
            'arrival_time' => $trip->getAttribute('arrive_at')->getTimestampMs(),
        ];

        $response = \GoogleMaps::load('directions')->setParam($params)->get();
        $response = json_decode($response);

        $route = $response->routes[0];

        $path = $route->overview_polyline->points;
        $waypointOrder = $route->waypoint_order;

        $orderedStudents = collect($waypointOrder)->reduce(function (Collection $orderedStudents, int $index, int $order) use ($students) {
            return $orderedStudents->put($students->get($index)->getKey(), ['order' => $order + 1]);
        }, collect())->all();

        $trip->setAttribute('path', $path);
        $trip->save();
        $trip->students()->sync($orderedStudents);
    }
}
