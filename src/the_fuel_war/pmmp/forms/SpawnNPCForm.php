<?php


namespace the_fuel_war\pmmp\forms;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use the_fuel_war\pmmp\entities\GameCreationComputer;
use the_fuel_war\pmmp\entities\GameListBulletinBoard;
use the_fuel_war\pmmp\utilities\SpawnNPC;
use the_fuel_war\types\GameType;

class SpawnNPCForm extends SimpleForm
{

    public function __construct() {
        parent::__construct("NPC", "", [
            new SimpleFormButton(
                "ソロのゲーム一覧",
                null,
                function (Player $player) {
                    if (!$player->isOp()) return;
                    SpawnNPC::execute(GameListBulletinBoard::NAME, $player->getLocation(), GameType::Solo());
                }
            ),
            new SimpleFormButton(
                "２人チームのゲーム一覧",
                null,
                function (Player $player) {
                    if (!$player->isOp()) return;
                    SpawnNPC::execute(GameListBulletinBoard::NAME, $player->getLocation(), GameType::TwoPlayers());
                }
            ),
            new SimpleFormButton(
                GameCreationComputer::NAME,
                null,
                function (Player $player) {
                    if (!$player->isOp()) return;
                    SpawnNPC::execute(GameCreationComputer::NAME, $player->getLocation());
                }
            )
        ]);
    }

    function onClickCloseButton(Player $player): void {
    }
}