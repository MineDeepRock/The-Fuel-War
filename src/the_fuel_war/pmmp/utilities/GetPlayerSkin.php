<?php


namespace the_fuel_ward\pmmp\utilities;


use the_fuel_ward\DataFolderPath;
use pocketmine\entity\Skin;
use pocketmine\Player;

class GetPlayerSkin
{
    static function execute(Player $player): Skin {
        return new Skin("Standard_CustomSlim", file_get_contents(DataFolderPath::$playerSkin . $player->getName() . ".skin"));
    }
}