<?php


namespace the_fuel_war\pmmp\slot_menus;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\models\Map;
use the_fuel_war\pmmp\forms\BloodPackSpawnVectorListForm;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddBloodPackSpawnVectorSlotMenu extends SlotMenu
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

                        $player->sendForm(new BloodPackSpawnVectorListForm($updatedMap));
                    },
                    function (Player $player, Block $block) {
                        $updatedMap = $this->updateMap($block);
                        SlotMenuSystem::close($player);

                        $player->sendForm(new BloodPackSpawnVectorListForm($updatedMap));
                    }
                ),
                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new BloodPackSpawnVectorListForm($this->map));
                    },
                    null,
                    8
                )
            ]
        );
    }


    private function updateMap(Vector3 $vector3): Map {

        $newBloodSpawnVectors = $this->map->getBloodPackSpawnVectorList();
        $newBloodSpawnVectors[] = $vector3;

        MapDAO::updatePartOfMap($this->map->getName(), ["blood_pack_list" => $newBloodSpawnVectors]);

        return MapDao::findByName($this->map->getName());
    }
}