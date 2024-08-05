<?php

namespace App\Traits;

use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Location;

trait FacilityCreateTrait
{

    public function createLocation($latitude, $longitude, $address, $country, $state, $city)
    {
        $location = Location::create([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
            'country' => $country,
            'state' => $state,
            'city' => $city
        ]);
        return $location;
    }


    public function createFacility($name, $description, $location_id, $user_id, $img)
    {
        $image = $this->saveImage($img);

        $facility = Facility::create([
            'name' => $name,
            'description' => $description,
            'location_id' => $location_id,
            'user_id' => $user_id,
            'img' => $image
        ]);
        return $facility;
    }
}
