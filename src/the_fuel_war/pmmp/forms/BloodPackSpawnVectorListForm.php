<?php


namespace the_fuel_ward\pmmp\forms;


use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\slot_menus\AddBloodPackSpawnVectorSlotMenu;
use the_fuel_ward\pmmp\slot_menus\BloodPackSpawnVectorSettingSlotMenu;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class BloodPackSpawnVectorListForm extends SimpleForm
{
    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        $buttons = [
            new SimpleFormButton(
                "輸血パックのスポーン地点を追加",
                null,
                function (Player $player) use ($map) {
                    SlotMenuSystem::send($player, new AddBloodPackSpawnVectorSlotMenu($map));
                }
            )
        ];

        foreach ($map->getBloodPackSpawnVectorList() as $bloodPackSpawnVector) {
            $buttons[] = new SimpleFormButton(
                "x:{$bloodPackSpawnVector->getY()},y:{$bloodPackSpawnVector->getY()},z:{$bloodPackSpawnVector->getZ()}",
                null,
                function (Player $player) use ($map, $bloodPackSpawnVector) {
                    $player->teleport($bloodPackSpawnVector);
                    SlotMenuSystem::send($player, new BloodPackSpawnVectorSettingSlotMenu($map, $bloodPackSpawnVector));
                }
            );
        }

        parent::__construct($map->getName() . "輸血パックのスポーン地点の設定", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }
}