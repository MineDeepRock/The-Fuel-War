<?php


namespace the_fuel_war\pmmp\services;


use pocketmine\Server;
use the_fuel_war\models\Map;
use the_fuel_war\pmmp\entities\BloodPackEntity;
use the_fuel_war\pmmp\entities\CadaverEntity;
use the_fuel_war\pmmp\entities\DyingPlayerEntity;
use the_fuel_war\pmmp\entities\FuelEntity;
use the_fuel_war\pmmp\entities\FuelTankEntity;
use the_fuel_war\pmmp\entities\ItemGunEntity;
use the_fuel_war\pmmp\entities\MedicineKitOnMapEntity;

class CleanMapPMMPService
{
    static function execute(Map $map): void {
        $level = Server::getInstance()->getLevelByName($map->getLevelName());

        foreach ($level->getEntities() as $entity) {
            switch (true) {
                case $entity instanceof BloodPackEntity:
                    $entity->kill();
                    continue;
                case $entity instanceof CadaverEntity:
                    $entity->kill();
                    continue;
                case $entity instanceof DyingPlayerEntity:
                    $entity->kill();
                    continue;
                case $entity instanceof FuelEntity:
                    $entity->kill();
                    continue;
                case $entity instanceof FuelTankEntity:
                    $entity->kill();
                    continue;
                case $entity instanceof ItemGunEntity:
                    $entity->kill();
                    continue;
                case $entity instanceof MedicineKitOnMapEntity:
                    $entity->kill();
                    continue;
            }
        }
    }
}