<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\data\ItemDataOnMap;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\forms\EditItemDataOnMapForm;
use the_fuel_ward\pmmp\forms\ItemDataOnMapListForm;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class ItemDataOnMapSettingSlotMenu extends SlotMenu
{

    private Map $map;

    public function __construct(Map $map, ItemDataOnMap $itemDataOnMap) {
        $this->map = $map;
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::TNT,
                    "削除",
                    function (Player $player) use ($itemDataOnMap) {
                        $updatedMap = $this->updateMap($itemDataOnMap->getVector());
                        SlotMenuSystem::close($player);

                        $player->sendForm(new ItemDataOnMapListForm($updatedMap));
                    }
                ),
                new SlotMenuElement(
                    ItemIds::NAME_TAG,
                    "アイテムを変更",
                    function (Player $player) use ($itemDataOnMap) {
                        SlotMenuSystem::close($player);

                        $player->sendForm(new EditItemDataOnMapForm($this->map, $itemDataOnMap));
                    }
                ),

                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new ItemDataOnMapListForm($this->map));
                    },
                    null,
                    8
                )
            ]
        );
    }


    private function updateMap(Vector3 $vector3): Map {
        $newItemDataOnMapList = [];
        foreach ($this->map->getItemDataOnMapList() as $itemDataOnMap) {
            if (!$itemDataOnMap->getVector()->equals($vector3)) {
                $newItemDataOnMapList[] = $itemDataOnMap;
            }
        }

        MapDAO::updatePartOfMap($this->map->getName(), ["item_data_list" => $newItemDataOnMapList]);

        return MapDao::findByName($this->map->getName());
    }
}