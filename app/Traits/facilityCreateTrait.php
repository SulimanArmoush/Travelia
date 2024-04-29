<?php

namespace App\Traits;
use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Location;

trait facilityCreateTrait
{

    public function createLocation($latitude,$longitude,$area_id)
    {
        $location = Location::create([
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'area_id' =>$area_id,
        ]);
        return $location->id;
    }

    public function createFacility($name,$description,$location_id,$user_id)
    {
        $facility = Facility::create([
            'name'=>$name,
            'description'=>$description,
            'location_id'=>$location_id,
            'user_id'=>$user_id,
        ]);
        return $facility->id;
    }
}
