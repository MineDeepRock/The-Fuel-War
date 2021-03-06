<?php


namespace the_fuel_war\pmmp\services;


use the_fuel_war\models\Map;
use the_fuel_war\pmmp\entities\BloodPackEntity;
use pocketmine\Server;

class SpawnBloodPackPMMPService
{
    static function execute(Map $map): void {
        $level = Server::getInstance()->getLevelByName($map->getLevelName());
        if ($level === null) return;

        foreach ($map->getBloodPackSpawnVectorList() as $vector3) {
            $entity = new BloodPackEntity($level, $vector3);
            $entity->spawnToAll();
        }
    }
}