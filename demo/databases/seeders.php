<?php


use Illuminate\Support\Arr;
use Lego\Demo\Models\City;
use Lego\Demo\Tools\ChinaRegions;

(function () {
    $regions = new ChinaRegions();
    $cities = Arr::flatten($regions->getCities(), 1);
    for ($i = 0; $i < 99; $i++) {
        $city = new City();
        $city->name = array_shift($cities)['name'];
        $city->save();
        echo "City[$city->name] created <br>";
    }
})();
