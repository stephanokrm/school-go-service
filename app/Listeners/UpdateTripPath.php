<?php

namespace App\Listeners;

use App\Events\TripUpdated;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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
        $isInProgress = $trip->getStartedAt() instanceof Carbon && $trip->getFinishedAt() === null;
        $latitude = $trip->getAttribute('latitude');
        $longitude = $trip->getAttribute('longitude');
        $itineraryAddressPlaceId = $trip->getItinerary()->getAddress()->getAttribute('place_id');
        $schoolAddressPlaceId = $trip->getItinerary()->getSchool()->getAddress()->getAttribute('place_id');

        if ($isInProgress && $latitude !== null && $longitude !== null) {
            $origin = "{$latitude},{$longitude}";
        } else {
            if ($newOriginPlaceId = $event->getOrigin()) {
                $originPlaceId = $newOriginPlaceId;
            } else {
                $originPlaceId = $trip->isRound() ? $schoolAddressPlaceId : $itineraryAddressPlaceId;
            }

            $origin = "place_id:{$originPlaceId}";
        }

        $destinationPlaceId = $trip->isRound() ? $itineraryAddressPlaceId : $schoolAddressPlaceId;
        $destination = "place_id:{$destinationPlaceId}";

        $completed = $trip
            ->students()
            ->where('absent', false)
            ->when($trip->isRound(), function (Builder $query) {
                $query->whereNotNull('student_trip.disembarked_at');
            })
            ->when(!$trip->isRound(), function (Builder $query) {
                $query->whereNotNull('student_trip.embarked_at');
            })
            ->count();
        $students = $trip
            ->students()
            ->where('absent', false)
            ->when($trip->isRound(), function (Builder $query) {
                $query->whereNull('student_trip.disembarked_at');
            })
            ->when(!$trip->isRound(), function (Builder $query) {
                $query->whereNull('student_trip.embarked_at');
            })
            ->get();

        $waypoints = $students->isEmpty() ? null : $students->reduce(function (string $waypoints, Student $student) {
            return "{$waypoints}|place_id:{$student->getAddress()->getAttribute('place_id')}";
        }, 'optimize:true');

        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'waypoints' => $waypoints,
            'alternatives' => false,
            'mode' => 'driving',
            'arrival_time' => $trip->getAttribute('arrive_at')->getTimestampMs(),
            'language' => 'pt-BR'
        ];

        $response = \GoogleMaps::load('directions')->setParam($params)->get();
        $response = json_decode($response);

        $route = $response->routes[0];

        $trip->setAttribute('path', $route->overview_polyline->points);
        $trip->save();

        if ($students->isNotEmpty()) {
            $orderedStudents = collect($route->waypoint_order)
                ->reduce(function (Collection $orderedStudents, int $index, int $order) use ($completed, $students) {
                    return $orderedStudents->put($students->get($index)->getKey(), ['order' => $completed + $order + 1]);
                }, collect())
                ->all();

            $trip->students()->syncWithoutDetaching($orderedStudents);
        }
    }
}
