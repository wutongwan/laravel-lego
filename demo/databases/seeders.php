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
    $regions = new ChinaRegions();
    $regionsCities = Arr::flatten($regions->getCities(), 1);
    $cities = [];
    for ($i = 0; $i < 99; $i++) {
        $city = new City();
        $city->name = array_shift($regionsCities)['name'];
        $city->save();
        $cities[] = $city;
        echo "City[$city->name] created <br>";
    }

    // create streets
    $streets = [];
    for ($i = 0; $i < 20; $i++) {
        $street = new Street();
        $street->city()->associate(Arr::random($cities));
        $street->name = $faker->name . $faker->randomElement(['路', '胡同']);
        $street->save();
        $streets[] = $street;
        echo "Street[$street->name] created <br>";
    }

    // create suites
    for ($i = 0; $i < 200; $i++) {
        $suite = new Suite();
        $suite->street()->associate($street = Arr::random($streets));
        $suite->address = $faker->address . $street->name . $faker->buildingNumber . '号';
        $suite->type = $faker->randomElement(Suite::listType());
        $suite->status = $faker->randomElement(Suite::listStatus());
        $suite->note = $faker->realText(250);
        $suite->save();
        echo "Suite[$suite->address] created <br>";
    }
})();
