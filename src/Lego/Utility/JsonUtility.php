<?php

namespace Lego\Utility;

use Illuminate\Support\Arr;
use JsonException;

/**
 * Class JsonUtility
 * @package  Lego\Utility
 *
 * @template J of string|array|object
 */
class JsonUtility
{
    /**
     * @page J $json
     * @param string $path
     * @return mixed
     */
    public static function get($json, string $path)
    {
        if (empty($json)) {
            return null;
        }

        if (is_string($json)) {
            return Arr::get(self::decode($json), $path);
        }

        if (is_array($json)) {
            return Arr::get($json, $path);
        }

        return data_get($json, $path);
    }

    /**
     * @param J $json
     * @param string $path
     * @param mixed $value
     * @return J
     */
    public static function set($json, string $path, $value)
    {
        if (empty($json)) {
            $array = [];
        } elseif (is_string($json)) {
            $array = self::decode($json);
        }
        if (isset($array)) {
            Arr::set($array, $path, $value);
            return json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_array($json)) {
            Arr::set($json, $path, $value);
            return $json;
        }

        data_set($json, $path, $value);
        return $json;
    }

    public static function decode(string $json): array
    {
        $array = json_decode($json, true);
        if ($array === null) {
            throw new JsonException('Cannot decode json: ' . json_last_error_msg());
        }
        return $array;
    }
}
