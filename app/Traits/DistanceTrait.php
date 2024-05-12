<?php

namespace App\Traits;

trait DistanceTrait
{
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
//$distance = distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);
}
