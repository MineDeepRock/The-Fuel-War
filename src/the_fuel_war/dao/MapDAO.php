<?php

namespace the_fuel_war\dao;


use the_fuel_war\dto\MapDTO;
use the_fuel_war\DataFolderPath;
use the_fuel_war\models\Map;

class MapDAO
{
    static function findByName(string $name): ?Map {
        if (!file_exists(DataFolderPath::$map . $name . ".json")) return null;

        $mapsData = json_decode(file_get_contents(DataFolderPath::$map . $name . ".json"), true);
        return MapDTO::decode($mapsData);
    }

    /**
     * @return Map[]
     */
    static function all(): array {
        $maps = [];
        $dh = opendir(DataFolderPath::$map);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::$map . $fileName) === "file") {
                $data = json_decode(file_get_contents(DataFolderPath::$map . $fileName), true);
                $maps[] = MapDTO::decode($data);
            }
        }

        closedir($dh);

        return $maps;
    }

    static function save(Map $map): void {
        if (self::findByName($map->getName()) !== null) return;

        file_put_contents(DataFolderPath::$map . $map->getName() . ".json", json_encode(MapDTO::encode($map)));
    }

    static function update(string $mapName, Map $map): void {
        if (self::findByName($map->getName()) === null) return;
        if ($mapName !== $map->getName()) self::delete($mapName);

        file_put_contents(DataFolderPath::$map . $map->getName() . ".json", json_encode(MapDTO::encode($map)));
    }

    static function delete(string $mapName): void {
        unlink(DataFolderPath::$map . $mapName . ".json");
    }

    static function updatePartOfMap(string $name, array $array): void {
        $map = self::findByName($name);
        if ($map === null) return;

        $newMap = new Map(
            array_key_exists("level_name", $array) ? $array["level_name"] : $map->getLevelName(),
            array_key_exists("name", $array) ? $array["name"] : $map->getName(),
            array_key_exists("start_vector", $array) ? $array["start_vector"] : $map->getStartVector(),
            array_key_exists("fuel_tanks", $array) ? $array["fuel_tanks"] : $map->getFuelTankMapDataList(),
            array_key_exists("fuel_spawn_vectors", $array) ? $array["fuel_spawn_vectors"] : $map->getFuelSpawnVectors(),
            array_key_exists("item_data_list", $array) ? $array["item_data_list"] : $map->getItemDataOnMapList(),
            array_key_exists("gun_data_list", $array) ? $array["gun_data_list"] : $map->getGunDataOnMapList(),
            array_key_exists("blood_pack_list", $array) ? $array["blood_pack_list"] : $map->getGunDataOnMapList(),
        );

        self::update($map->getName(), $newMap);
    }
}