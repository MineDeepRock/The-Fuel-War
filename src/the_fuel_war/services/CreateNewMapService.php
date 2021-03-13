<?php


namespace the_fuel_war\services;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\models\Map;
use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;

class CreateNewMapService
{
    static function execute(string $levelName, string $mapName, Vector3 $spawnPoint): bool {
        if ($levelName === null or empty($mapName)) return false;

        $newMap = new Map(
            $levelName,
            $mapName,
            $spawnPoint,
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