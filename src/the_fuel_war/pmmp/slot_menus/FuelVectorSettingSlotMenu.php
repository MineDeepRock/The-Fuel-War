<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\forms\FuelSpawnVectorSettingForm;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class FuelVectorSettingSlotMenu extends SlotMenu
{

    private Map $map;

    public function __construct(Map $map, Vector3 $vector3) {
        $this->map = $map;
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::TNT,
                    "削除",
                    function (Player $player) use ($vector3) {
                        $updatedMap = $this->updateMap($vector3);
                        SlotMenuSystem::close($player);

                        $player->sendForm(new FuelSpawnVectorSettingForm($updatedMap));
                    }
                ),

                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player)  {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new FuelSpawnVectorSettingForm($this->map));
                    },
                    null,
                    8
                )
            ]
        );
    }


    private function updateMap(Vector3 $vector3): Map {
        $newFuelSpawnVectors = [];
        foreach ($this->map->getFuelSpawnVectors() as $fuelSpawnVector) {
            if (!$fuelSpawnVector->equals($vector3)) {
                $newFuelSpawnVectors[] = $fuelSpawnVector;
            }

        }

        MapDAO::updatePartOfMap($this->map->getName(),["fuel_spawn_vectors" => $newFuelSpawnVectors]);

        return MapDao::findByName($this->map->getName());
    }
}