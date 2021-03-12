<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\data\GunDataOnMap;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\forms\EditGunDataOnMapForm;
use the_fuel_ward\pmmp\forms\GunDataOnMapListForm;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class GunDataOnMapSettingSlotMenu extends SlotMenu
{

    private Map $map;

    public function __construct(Map $map, GunDataOnMap $gunDataOnMap) {
        $this->map = $map;
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::TNT,
                    "削除",
                    function (Player $player) use ($gunDataOnMap) {
                        $updatedMap = $this->updateMap($gunDataOnMap->getVector());
                        SlotMenuSystem::close($player);

                        $player->sendForm(new GunDataOnMapListForm($updatedMap));
                    }
                ),
                new SlotMenuElement(
                    ItemIds::NAME_TAG,
                    "銃を変更",
                    function (Player $player) use ($gunDataOnMap) {
                        SlotMenuSystem::close($player);

                        $player->sendForm(new EditGunDataOnMapForm($this->map, $gunDataOnMap));
                    }
                ),

                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new GunDataOnMapListForm($this->map));
                    },
                    null,
                    8
                )
            ]
        );
    }


    private function updateMap(Vector3 $vector3): Map {
        $newGunDataOnMap = [];
        foreach ($this->map->getGunDataOnMapList() as $gunDataOnMap) {
            if (!$gunDataOnMap->getVector()->equals($vector3)) {
                $newGunDataOnMap[] = $gunDataOnMap;
            }
        }

        MapDAO::updatePartOfMap($this->map->getName(), ["gun_data_list" => $newGunDataOnMap]);

        return MapDao::findByName($this->map->getName());
    }
}