<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\pmmp\services\JoinGamePMMPService;
use the_fuel_war\services\JoinGameService;
use the_fuel_war\storages\GameStorage;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;

class GameListForm extends SimpleForm
{

    private TaskScheduler $scheduler;

    public function __construct(Player $player, TaskScheduler $scheduler) {
        $availableGamesAsButtons = [];
        $unavailableGamesAsButtons = [];
        $this->scheduler = $scheduler;


        foreach (GameStorage::getAll() as $game) {
            $gameId = $game->getGameId();
            $mapName = TextFormat::BOLD . $game->getMap()->getName() . TextFormat::RESET;
            $playersCountAsText = TextFormat::BOLD . count($game->getPlayerNameList()) . "/" . $game->getMaxPlayers() . TextFormat::RESET;
            $text = "マップ:{$mapName},人数:{$playersCountAsText},オーナー:{$game->getGameOwnerName()}";

            if ($game->canJoin($player->getName())) {
                $availableGamesAsButtons[] = new SimpleFormButton(
                    TextFormat::GREEN . "参加可能" . TextFormat::RESET . $text,
                    null,
                    function (Player $player) use ($gameId): void {
                        $result = JoinGameService::execute($gameId, $player->getName(), $this->scheduler);
                        if ($result) {
                            JoinGamePMMPService::execute($player, $gameId);
                        } else {
                            $player->sendMessage("ゲームに参加できませんでした");
                        }
                    }
                );

            } else {
                $unavailableGamesAsButtons[] = new SimpleFormButton(
                    TextFormat::RED . "参加不可能" . TextFormat::RESET . $text,
                    null,
                    function (Player $player) {
                        $player->sendForm(new GameListForm($player, $this->scheduler));
                    }
                );

            }
        }

        $gameAsButtonList = array_merge($availableGamesAsButtons, $availableGamesAsButtons);
        array_unshift($gameAsButtonList, new SimpleFormButton(
            TextFormat::BLUE . "更新",
            null,
            function (Player $player) {
                $player->sendForm(new GameListForm($player, $this->scheduler));
            }
        ));

        parent::__construct("試合一覧", "タップで参加できます", $gameAsButtonList);
    }

    function onClickCloseButton(Player $player): void {
    }
}