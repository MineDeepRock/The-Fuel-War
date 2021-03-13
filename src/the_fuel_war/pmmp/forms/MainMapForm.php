<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\MapDAO;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;

class MainMapForm extends SimpleForm
{

    public function __construct() {
        $buttons = [new SimpleFormButton(
            "マップを追加",
            null,
            function (Player $player) {
                $player->sendForm(new CreateNewMapForm());
            }
        )];

        foreach (MapDAO::all() as $map) {
            $buttons[] = new SimpleFormButton(
                $map->getName(),
                null,
                function (Player $player) use ($map) {
                    $level = Server::getInstance()->getLevelByName($map->getLevelName());
                    $player->teleport($level->getSpawnLocation());

                    $player->sendForm(new MapSettingForm($map));
                }
            );
        }

        parent::__construct("", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}