<?php


namespace the_fuel_ward\pmmp\forms;


use the_fuel_ward\pmmp\utilities\GetWorldNameList;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;

class WorldListForm extends SimpleForm
{

    public function __construct() {
        $buttons = [];
        foreach (GetWorldNameList::execute() as $name) {
            $buttons[] = new SimpleFormButton(
                $name,
              null,
              function (Player $player) use ($name) {
                  $level = Server::getInstance()->getLevelByName($name);
                  $player->teleport($level->getSpawnLocation());
              }
            );
        }

        parent::__construct("ワールド一覧", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}