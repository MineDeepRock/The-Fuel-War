<?php


namespace the_fuel_war\pmmp\slot_menus;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\models\Map;
use the_fuel_war\pmmp\forms\FuelSpawnVectorSettingForm;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddFuelSpawnVectorSlotMenu extends SlotMenu
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

                        $player->sendForm(new FuelSpawnVectorSettingForm($updatedMap));
                    },
                    function (Player $player, Block $block) {
                        $updatedMap = $this->updateMap($block);
                        SlotMenuSystem::close($player);

                        $player->sendForm(new FuelSpawnVectorSettingForm($updatedMap));
                    }
                ),

                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player) {
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

        $newFuelSpawnVectors = $this->map->getFuelSpawnVectors();
        $newFuelSpawnVectors[] = $vector3;

        MapDAO::updatePartOfMap($this->map->getName(), ["fuel_spawn_vectors" => $newFuelSpawnVectors]);

        return MapDao::findByName($this->map->getName());
    }
}