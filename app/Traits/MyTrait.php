<?php

namespace App\Traits;

trait MyTrait
{
    function saveImage($photo)
    {
        $file_name = time() . '.' . $photo->getClientOriginalExtension();
        $file_name = $photo->store('images', 'public');
        $photo->move(public_path('images'), $file_name);

        return $file_name;
    }

    function areaSaveImage($photo)
    {
        $file_name = time() . '.' . $photo->getClientOriginalExtension();
        $file_name = $photo->store('areaPhoto', 'public');
        $photo->move(public_path('areaPhoto'), $file_name);

        return $file_name;
    }

    public function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $earthRadius = 6371;

        // تحويل من الدرجات إلى الراديان
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        // حساب الفروقات
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        // تطبيق صيغة هافرساين
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        // حساب المسافة
        return $angle * $earthRadius;
    }
}





/*    function upload($imgs)
    {
        $array = [];
        foreach ($imgs as $img) {
            $imge = $this->saveImage($img);
            $array[] = $imge;
        }
        return json_encode($array);
    }*/


