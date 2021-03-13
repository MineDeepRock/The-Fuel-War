<?php

namespace the_fuel_war\dto;


use the_fuel_war\data\FuelTankMapData;
use the_fuel_war\data\GunDataOnMap;
use the_fuel_war\data\ItemDataOnMap;
use the_fuel_war\models\Map;
use pocketmine\math\Vector3;

class MapDTO
{
    static function decode(array $json): Map {
        $fuelTankMapDataList = [];
        foreach ($json["fuel_tanks"] as $fuelTank) {
            $fuelTankMapDataList[] = new FuelTankMapData($fuelTank["capacity"], new Vector3($fuelTank["x"], $fuelTank["y"], $fuelTank["z"]));
        }


        $fuelSpawnVectors = [];
        foreach ($json["fuel_spawn_vectors"] as $fuelSpawnVector) {
            $fuelSpawnVectors[] = new Vector3($fuelSpawnVector["x"], $fuelSpawnVector["y"], $fuelSpawnVector["z"]);
        }

        $startVector = new Vector3(
            $json["start_vector"]["x"],
            $json["start_vector"]["y"],
            $json["start_vector"]["z"],
        );

        $itemDataList = [];
        foreach ($json["item_data_list"] as $item) {
            $vector = new Vector3(
                $item["x"],
                $item["y"],
                $item["z"],
            );
            $itemDataList[] = new ItemDataOnMap($item["name"], $vector);
        }

        $gunDataList = [];
        foreach ($json["gun_data_list"] as $gun) {
            $vector = new Vector3(
                $gun["x"],
                $gun["y"],
                $gun["z"],
            );
            $gunDataList[] = new GunDataOnMap($gun["name"], $vector);
        }

        $bloodPackList = [];
        foreach ($json["blood_pack_list"] as $bloodPack) {
            $bloodPackList[] = new Vector3(
                $bloodPack["x"],
                $bloodPack["y"],
                $bloodPack["z"],
            );
        }

        return new Map(
            $json["level_name"],
            $json["name"],
            $startVector,
            $fuelTankMapDataList,
            $fuelSpawnVectors,
            $itemDataList,
            $gunDataList,
            $bloodPackList
        );
    }

    static function encode(Map $map): array {

        $fuelTanks = [];
        foreach ($map->getFuelTankMapDataList() as $fueLTankMapData) {
            $fuelTanks[] = [
                "capacity" => $fueLTankMapData->getCapacity(),
                "x" => $fueLTankMapData->getVector()->getX(),
                "y" => $fueLTankMapData->getVector()->getY(),
                "z" => $fueLTankMapData->getVector()->getZ()
            ];
        }

        $fuelSpawnVectors = [];
        foreach ($map->getFuelSpawnVectors() as $fuelSpawnVector) {
            $fuelSpawnVectors[] = [
                "x" => $fuelSpawnVector->getX(),
                "y" => $fuelSpawnVector->getY(),
                "z" => $fuelSpawnVector->getZ(),
            ];
        }

        $startVector = [
            "x" => $map->getStartVector()->getX(),
            "y" => $map->getStartVector()->getY(),
            "z" => $map->getStartVector()->getZ(),
        ];

        $itemDataList = [];
        foreach ($map->getItemDataOnMapList() as $itemData) {
            $itemDataList[] = [
                "name" => $itemData->getName(),
                "x" => $itemData->getVector()->getX(),
                "y" => $itemData->getVector()->getY(),
                "z" => $itemData->getVector()->getZ()
            ];
        }


        $gunDataList = [];
        foreach ($map->getGunDataOnMapList() as $gunData) {
            $gunDataList[] = [
                "name" => $gunData->getName(),
                "x" => $gunData->getVector()->getX(),
                "y" => $gunData->getVector()->getY(),
                "z" => $gunData->getVector()->getZ()
            ];
        }

        $bloodPackList = [];
        foreach ($map->getBloodPackSpawnVectorList() as $bloodPack) {
            $bloodPackList[] = [
                "x" => $bloodPack->getX(),
                "y" => $bloodPack->getY(),
                "z" => $bloodPack->getZ(),
            ];
        }

        return [
            "level_name" => $map->getLevelName(),
            "name" => $map->getName(),
            "start_vector" => $startVector,
            "fuel_tanks" => $fuelTanks,
            "fuel_spawn_vectors" => $fuelSpawnVectors,
            "item_data_list" => $itemDataList,
            "gun_data_list" => $gunDataList,
            "blood_pack_list" => $bloodPackList
        ];
    }
}