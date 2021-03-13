<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\models\Map;
use the_fuel_war\pmmp\slot_menus\AddItemDataOnMapSlotMenu;
use the_fuel_war\pmmp\slot_menus\ItemDataOnMapSettingSlotMenu;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class ItemDataOnMapListForm extends SimpleForm
{
    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        $buttons = [
            new SimpleFormButton(
                "アイテムのスポーン地点を追加",
                null,
                function (Player $player) use ($map) {
                    SlotMenuSystem::send($player, new AddItemDataOnMapSlotMenu($map));
                }
            )
        ];

        foreach ($map->getItemDataOnMapList() as $itemDataOnMap) {
            $vector = $itemDataOnMap->getVector();

            $buttons[] = new SimpleFormButton(
                "アイテム名:{$itemDataOnMap->getName()},x:{$vector->getX()},y:{$vector->getY()},z:{$vector->getZ()}",
                null,
                function (Player $player) use ($map, $itemDataOnMap) {
                    $player->teleport($itemDataOnMap->getVector());
                    SlotMenuSystem::send($player, new ItemDataOnMapSettingSlotMenu($map, $itemDataOnMap));
                }
            );
        }

        parent::__construct($map->getName() . "アイテムのスポーン地点を追加", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }
}