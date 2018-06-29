<?php

namespace Lego\Field;

/**
 * 将 field name 分割成需要的格式
 * Class FieldNameSlicer.
 */
class FieldNameSlicer
{
    const RELATION_DELIMITER = '.';
    const JSON_DELIMITER = ':';
    const PIPE_DELIMITER = '|';
    const PIPE_ARG_DELIMITER = ',';

    /**
     * split name to relation, column and json path.
     *
     * @param string $name field name,  eg: school.city.column:json_key:sub_json_key
     *
     * @return array eg: [ ['school', 'city'], 'column', ['json_key', 'sub_json_key'] ]
     */
    public static function split(string $name): array
    {
        $parts = explode(self::JSON_DELIMITER, $name, 2);
        $jsonPath = count($parts) === 2 ? explode(self::JSON_DELIMITER, end($parts)) : [];

        $relationParts = explode(self::RELATION_DELIMITER, $parts[0]);

        return [
            array_slice($relationParts, 0, -1),
            end($relationParts),
            $jsonPath,
        ];
    }
}
