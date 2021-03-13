<?php


namespace the_fuel_war\pmmp\utilities;


use the_fuel_war\DataFolderPath;
use pocketmine\entity\Skin;
use pocketmine\Player;

class GetPlayerSkin
{
    static function execute(Player $player): Skin {
        return new Skin("Standard_CustomSlim", file_get_contents(DataFolderPath::$playerSkin . $player->getName() . ".skin"));
    }
}