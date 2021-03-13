<?php


namespace the_fuel_war\models;


use the_fuel_war\data\FuelTankMapData;
use the_fuel_war\data\GunDataOnMap;
use the_fuel_war\data\ItemDataOnMap;
use pocketmine\math\Vector3;

//TODO:ModelではなくDataではないか？？名前をMapDataにするべきか？
class Map
{
    private string $levelName;
    private string $name;

    private array $fuelTankMapDataList;
    private array $fuelSpawnVectors;

    private array $itemDataOnMapList;
    private array $gunDataOnMapList;
    private array $bloodPackSpawnVectorList;

    public function __construct(string $levelName, string $name,  array $fuelTankMapDataList, array $fuelSpawnVectors, array $itemDataOnMapList, array $gunDataOnMapList, array $bloodPackSpawnVectorList) {
        $this->levelName = $levelName;
        $this->name = $name;
        $this->fuelTankMapDataList = $fuelTankMapDataList;
        $this->fuelSpawnVectors = $fuelSpawnVectors;
        $this->itemDataOnMapList = $itemDataOnMapList;
        $this->gunDataOnMapList = $gunDataOnMapList;
        $this->bloodPackSpawnVectorList = $bloodPackSpawnVectorList;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return FuelTankMapData[]
     */
    public function getFuelTankMapDataList(): array {
        return $this->fuelTankMapDataList;
    }

    /**
     * @return Vector3[]
     */
    public function getFuelSpawnVectors(): array {
        return $this->fuelSpawnVectors;
    }

    /**
     * @return string
     */
    public function getLevelName(): string {
        return $this->levelName;
    }

    /**
     * @return GunDataOnMap[]
     */
    public function getGunDataOnMapList(): array {
        return $this->gunDataOnMapList;
    }

    /**
     * @return ItemDataOnMap[]
     */
    public function getItemDataOnMapList(): array {
        return $this->itemDataOnMapList;
    }

    /**
     * @return Vector3[]
     */
    public function getBloodPackSpawnVectorList(): array {
        return $this->bloodPackSpawnVectorList;
    }
}