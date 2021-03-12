<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\data\WaitingRoom;
use the_fuel_ward\pmmp\forms\WaitingRoomListForm;
use the_fuel_ward\storages\WaitingRoomStorage;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddWaitingRoomSlotMenu extends SlotMenu
{
    public function __construct() {
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::DIAMOND_BLOCK,
                    "選択",
                    function (Player $player) {
                        WaitingRoomStorage::add(new WaitingRoom($player->asVector3(), true));
                        SlotMenuSystem::close($player);

                        $player->sendForm(new WaitingRoomListForm());
                    },
                    function (Player $player, Block $block) {
                        WaitingRoomStorage::add(new WaitingRoom($block->asVector3(), true));
                        SlotMenuSystem::close($player);

                        $player->sendForm(new WaitingRoomListForm());
                    }
                ),
                new SlotMenuElement(
                    ItemIds::DROPPER,
                    "戻る",
                    function (Player $player) {
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