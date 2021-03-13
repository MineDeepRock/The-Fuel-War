<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\models\Map;
use the_fuel_war\pmmp\slot_menus\AddFuelTankSlotMenu;
use the_fuel_war\pmmp\slot_menus\FuelTankSettingSlotMenu;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class FuelTankListForm extends SimpleForm
{
    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        $buttons = [
            new SimpleFormButton(
                "タンクを追加",
                null,
                function (Player $player) use ($map) {
                    SlotMenuSystem::send($player, new AddFuelTankSlotMenu($map));
                }
            )
        ];

        foreach ($map->getFuelTankMapDataList() as $fuelTankMapData) {
            $vector = $fuelTankMapData->getVector();

            $buttons[] = new SimpleFormButton(
                "容量:{$fuelTankMapData->getCapacity()},x:{$vector->getX()},y:{$vector->getY()},z:{$vector->getZ()}",
                null,
                function (Player $player) use ($map, $fuelTankMapData) {
                    $player->teleport($fuelTankMapData->getVector());
                    SlotMenuSystem::send($player, new FuelTankSettingSlotMenu($map, $fuelTankMapData));
                }
            );
        }

        parent::__construct($map->getName() . "のタンクの設定", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }
}