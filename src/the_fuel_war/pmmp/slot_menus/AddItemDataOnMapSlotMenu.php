<?php


namespace the_fuel_ward\pmmp\slot_menus;


use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\forms\AddItemDataOnMapForm;
use the_fuel_ward\pmmp\forms\ItemDataOnMapListForm;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddItemDataOnMapSlotMenu extends SlotMenu
{

    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        parent::__construct(
            [
                new SlotMenuElement(
                    ItemIds::DIAMOND_BLOCK,
                    "選択",
                    function (Player $player) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new AddItemDataOnMapForm($this->map, $player));
                    },
                    function (Player $player, Block $block) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new AddItemDataOnMapForm($this->map, $block));
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
}