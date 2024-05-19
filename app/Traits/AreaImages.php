<?php

namespace App\Traits;

trait AreaImages
{
    function areaSaveImage($photo){
        $file_name = time().'.'.$photo -> getClientOriginalExtension();
        $file_name = $photo -> store('areaPhoto','public');
        $photo -> move(public_path('areaPhoto'),$file_name);

        return $file_name;
    }

    function areaUpload($imgs)
    {
        $array = [];
        foreach ($imgs as $img) {
            $imge = $this->areaSaveImage($img);
            $array[] = $imge;
        }
        return json_encode($array);
    }

}
