<?php


use Faker\Factory;
use Faker\Provider\zh_TW\Text;
use Illuminate\Support\Arr;
use Lego\Demo\Models\City;
use Lego\Demo\Models\Street;
use Lego\Demo\Models\Suite;
use Lego\Demo\Tools\ChinaRegions;

(function () {
    $faker = Factory::create('zh_CN');
    $faker->addProvider(new Text($faker));

    // create cities
    echo 'Cities: ';
    $cities = [];
    $regionsCities = Arr::flatten(($regions = new ChinaRegions())->getCities(), 1);
    for ($i = 0; $i < 99; $i++) {
        $city = new City();
        $city->name = array_shift($regionsCities)['name'];
        $city->save();
        $cities[] = $city;
        echo $city->name . "&nbsp;";
    }
    echo "<br><br><br>";

    // create streets
    echo 'Streets: ';
    $streets = [];
    for ($i = 0; $i < 20; $i++) {
        $street = new Street();
        $street->city()->associate(Arr::random($cities));
        $street->name = $faker->name . $faker->randomElement(['路', '胡同']);
        $street->save();
        $streets[] = $street;
        echo $street->name . "&nbsp;";
    }
    echo "<br><br><br>";

    // create suites
    echo 'Suites: ';
    for ($i = 0; $i < 200; $i++) {
        $suite = new Suite();
        $suite->street()->associate($street = Arr::random($streets));
        $suite->address = $faker->address . $street->name . $faker->buildingNumber . '号';
        $suite->type = $faker->randomElement(Suite::listType());
        $suite->status = $faker->randomElement(Suite::listStatus());
        $suite->note = $faker->realText(250);
        $suite->save();
        echo $suite->address . "&nbsp;";
    }
})();
