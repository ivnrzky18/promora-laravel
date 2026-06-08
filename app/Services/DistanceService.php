<?php

namespace App\Services;

class DistanceService
{
    /**
     * Menghitung jarak antara dua titik koordinat menggunakan formula Haversine.
     * Mengembalikan jarak dalam kilometer.
     *
     * @param float $lat1  Latitude titik pertama (Consumer)
     * @param float $lng1  Longitude titik pertama (Consumer)
     * @param float $lat2  Latitude titik kedua (Seller)
     * @param float $lng2  Longitude titik kedua (Seller)
     * @return float Jarak dalam kilometer
     */
    public function calculate(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
