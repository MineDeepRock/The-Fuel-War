<?php


namespace the_fuel_war\pmmp\slot_menus;


use the_fuel_war\models\Map;
use the_fuel_war\pmmp\forms\AddGunDataOnMapForm;
use the_fuel_war\pmmp\forms\GunDataOnMapListForm;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddGunDataOnMapSlotMenu extends SlotMenu
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
                        $player->sendForm(new AddGunDataOnMapForm($this->map, $player));
                    },
                    function (Player $player, Block $block) {
                        SlotMenuSystem::close($player);
                        $player->sendForm(new AddGunDataOnMapForm($this->map, $block));
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
}