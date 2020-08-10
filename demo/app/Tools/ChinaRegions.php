<?php

namespace Lego\Demo\Tools;

class ChinaRegions
{
    private $urls = [
        'province' => 'https://github.com/wecatch/china_regions/raw/master/json/province.json',
        'city' => 'https://raw.githubusercontent.com/wecatch/china_regions/master/json/city.json',
        'county' => 'https://raw.githubusercontent.com/wecatch/china_regions/master/json/county.json',
        'town' => 'https://github.com/wecatch/china_regions/raw/master/json/town.json',
    ];

    public function getProvinces()
    {
        return $this->get('province');
    }

    public function getCities()
    {
        return $this->get('city');
    }

    public function getCounties()
    {
        return $this->get('county');
    }


    public function getTowns()
    {
        return $this->get('town');
    }

    private function get(string $type)
    {
        if (!isset($this->urls[$type])) {
            throw new \InvalidArgumentException('invalid type');
        }

        $path = storage_path($type . '.json');
        if (file_exists($path)) {
            return \json_decode(file_get_contents($path), true);
        }

        $content = file_get_contents($this->urls[$type]);
        file_put_contents($path, $content);
        return \json_decode($content, true);
    }
}
