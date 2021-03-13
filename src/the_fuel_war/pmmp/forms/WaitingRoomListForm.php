<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\pmmp\slot_menus\AddWaitingRoomSlotMenu;
use the_fuel_war\pmmp\slot_menus\WaitingRoomSettingSlotMenu;
use the_fuel_war\storages\WaitingRoomStorage;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class WaitingRoomListForm extends SimpleForm
{

    public function __construct() {
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) {
                    SlotMenuSystem::send($player, new AddWaitingRoomSlotMenu());
                }
            ),
        ];

        foreach (WaitingRoomStorage::getAll() as $waitingRoom) {
            $vector = $waitingRoom->getVector();
            $buttons[] = new SimpleFormButton(
                "x:{$vector->getX()},y:{$vector->getY()},z:{$vector->getZ()}",
                null,
                function (Player $player) use ($waitingRoom) {
                    SlotMenuSystem::send($player, new WaitingRoomSettingSlotMenu($waitingRoom));
                }
            );
        }

        parent::__construct("待機室", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}