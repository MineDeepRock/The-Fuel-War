<?php


namespace the_fuel_war\pmmp\slot_menus;


use the_fuel_war\data\WaitingRoom;
use the_fuel_war\pmmp\forms\WaitingRoomListForm;
use the_fuel_war\storages\WaitingRoomStorage;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class WaitingRoomSettingSlotMenu extends SlotMenu
{
    public function __construct(WaitingRoom $waitingRoom) {
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::TNT,
                    "削除",
                    function (Player $player) use ($waitingRoom) {
                        WaitingRoomStorage::delete($waitingRoom);
                        SlotMenuSystem::close($player);

                        $player->sendForm(new WaitingRoomListForm());
                    }
                ),

                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player)  {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new WaitingRoomListForm());
                    },
                    null,
                    8
                )
            ]
        );
    }
}