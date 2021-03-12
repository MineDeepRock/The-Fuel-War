<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\data\FuelTankMapData;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\forms\FuelTankListForm;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddFuelTankSlotMenu extends SlotMenu
{

    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::DIAMOND_BLOCK,
                    "選択",
                    function (Player $player) {
                        $updatedMap = $this->updateMap($player);
                        SlotMenuSystem::close($player);

                        $player->sendForm(new FuelTankListForm($updatedMap));
                    },
                    function (Player $player, Block $block) {
                        $updatedMap = $this->updateMap($block);
                        SlotMenuSystem::close($player);

                        $player->sendForm(new FuelTankListForm($updatedMap));
                    }
                ),
                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new FuelTankListForm($this->map));
                    },
                    null,
                    8
                )
            ]
        );
    }


    private function updateMap(Vector3 $vector3): Map {

        $newFuelTankVectors = $this->map->getFuelTankMapDataList();
        $newFuelTankVectors[] = new FuelTankMapData(20, $vector3);

        MapDAO::updatePartOfMap($this->map->getName(), ["fuel_tanks" => $newFuelTankVectors]);

        return MapDao::findByName($this->map->getName());
    }
}