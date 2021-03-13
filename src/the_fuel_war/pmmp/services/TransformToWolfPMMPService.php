<?php


namespace the_fuel_war\pmmp\services;


use the_fuel_war\DataFolderPath;
use the_fuel_war\pmmp\PlayerInventoryContentsStorage;
use the_fuel_war\storages\PlayerStatusStorage;
use pocketmine\entity\Attribute;
use pocketmine\entity\Skin;
use pocketmine\Player;

class TransformToWolfPMMPService
{
    static function execute(Player $player): void {
        $playerStatus = PlayerStatusStorage::findByName($player->getName());
        if ($playerStatus === null) return;
        if ($playerStatus->canTransform()) {
            $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.30);
            $player->setScale(1.3);
            $player->setSkin(new Skin("Standard_CustomSlim", file_get_contents(DataFolderPath::$skin . "wolf.skin")));
            $player->sendSkin();
            $playerStatus->startTransformTimer();

            //インベントリを保存しクリア
            PlayerInventoryContentsStorage::save($player->getName(), $player->getInventory()->getContents());
            $player->getInventory()->setContents([]);
        }
    }
}