<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Collection;

class AddressService
{
    /**
     * @param Collection $attributes
     * @return Address
     */
    public function store(Collection $attributes): Address
    {
        $location = $this->getLocation($attributes->get('place_id'));

        $address = new Address();
        $address->fill($attributes->only($address->getFillable())->all());
        $address->setAttribute('latitude', $location->lat);
        $address->setAttribute('longitude', $location->lng);
        $address->save();

        return $address;
    }

    /**
     * @param Address $address
     * @param Collection $attributes
     * @return Address
     */
    public function update(Address $address, Collection $attributes): Address
    {
        $address->fill($attributes->only($address->getFillable())->all());

        if ($address->wasChanged('place_id')) {
            $location = $this->getLocation($attributes->get('place_id'));

            $address->setAttribute('latitude', $location->lat);
            $address->setAttribute('longitude', $location->lng);
        }

        $address->save();

        return $address;
    }

    /**
     * @param string $place
     * @return object
     */
    private function getLocation(string $place): object
    {
        $response = \GoogleMaps::load('geocoding')
            ->setParamByKey('place_id', $place)
            ->get();

        return json_decode($response)->results[0]->geometry->location;
    }
}
