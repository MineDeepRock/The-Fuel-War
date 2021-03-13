<?php


namespace the_fuel_war\services;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\models\Map;

class CreateNewMapService
{
    static function execute(string $levelName, string $mapName): bool {
        if ($levelName === null or empty($mapName)) return false;

        $newMap = new Map(
            $levelName,
            $mapName,
            [],
            [],
            [],
            [],
            [],
        );

        MapDAO::save($newMap);

        return true;
    }
}