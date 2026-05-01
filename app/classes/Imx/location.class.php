<?php

namespace Imx;


class location
{
    /**
     *  get_meters_between_points function
     *  Function to ge distance in meters betwen two coordinates 
     * 
     * @param mixed $latitude1 
     * @param mixed $longitude1 
     * @param mixed $latitude2 
     * @param mixed $longitude2 
     * @return int|float 
     */
    static  function get_meters_between_points($latitude1, $longitude1, $latitude2, $longitude2){
        if (($latitude1 == $latitude2) && ($longitude1 == $longitude2)) {
            return 0;
        } // distance is zero because they're the same point
        $p1 = deg2rad($latitude1*1);
        $p2 = deg2rad($latitude2*1);
        $dp = deg2rad($latitude2 - $latitude1);
        $dl = deg2rad($longitude2 - $longitude1);
        $a = (sin($dp / 2) * sin($dp / 2)) + (cos($p1) * cos($p2) * sin($dl / 2) * sin($dl / 2));
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $r = 6371008; // Earth's average radius, in meters
        $d = $r * $c;
        return $d; // distance, in meters
    }

    static  function get_distance_between_points($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $meters = self::get_meters_between_points($latitude1, $longitude1, $latitude2, $longitude2);
        $kilometers = $meters / 1000;
        $miles = $meters / 1609.34;
        $yards = $miles * 1760;
        $feet = $miles * 5280;
        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }
}
