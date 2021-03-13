<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\models\Map;
use the_fuel_war\pmmp\slot_menus\AddGunDataOnMapSlotMenu;
use the_fuel_war\pmmp\slot_menus\GunDataOnMapSettingSlotMenu;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class GunDataOnMapListForm extends SimpleForm
{
    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        $buttons = [
            new SimpleFormButton(
                "銃のスポーン地点を追加",
                null,
                function (Player $player) use ($map) {
                    SlotMenuSystem::send($player, new AddGunDataOnMapSlotMenu($map));
                }
            )
        ];

        foreach ($map->getGunDataOnMapList() as $gunDataOnMap) {
            $vector = $gunDataOnMap->getVector();

            $buttons[] = new SimpleFormButton(
                "銃:{$gunDataOnMap->getName()},x:{$vector->getX()},y:{$vector->getY()},z:{$vector->getZ()}",
                null,
                function (Player $player) use ($map, $gunDataOnMap) {
                    $player->teleport($gunDataOnMap->getVector());
                    SlotMenuSystem::send($player, new GunDataOnMapSettingSlotMenu($map, $gunDataOnMap));
                }
            );
        }

        parent::__construct($map->getName() . "銃のスポーン地点の設定", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }
}