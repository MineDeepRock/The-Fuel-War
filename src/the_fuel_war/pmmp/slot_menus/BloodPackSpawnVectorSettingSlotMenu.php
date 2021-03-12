<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\forms\BloodPackSpawnVectorListForm;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class BloodPackSpawnVectorSettingSlotMenu extends SlotMenu
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

                        $player->sendForm(new BloodPackSpawnVectorListForm($updatedMap));
                    }
                ),

                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player)  {
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
        $newBloodPackSpawnVectors = [];
        foreach ($this->map->getBloodPackSpawnVectorList() as $bloodPackSpawnVector) {
            if (!$bloodPackSpawnVector->equals($vector3)) {
                $newBloodPackSpawnVectors[] = $bloodPackSpawnVector;
            }

        }

        MapDAO::updatePartOfMap($this->map->getName(),["blood_pack_list" => $newBloodPackSpawnVectors]);

        return MapDao::findByName($this->map->getName());
    }
}