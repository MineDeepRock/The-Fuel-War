<?php


namespace the_fuel_war\pmmp\utilities;


use the_fuel_war\DataFolderPath;
use pocketmine\Player;

class SavePlayerSkin
{
    static function execute(Player $player): void {
        $skin = $player->getSkin();
        file_put_contents(DataFolderPath::$playerSkin . $player->getName() . ".skin", $skin->getSkinData());
    }
}