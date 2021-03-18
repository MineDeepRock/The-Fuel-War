<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\data\PlayerData;
use the_fuel_war\pmmp\services\JoinGamePMMPService;
use the_fuel_war\services\JoinGameService;
use the_fuel_war\services\JoinRandomGameService;
use the_fuel_war\storages\GameStorage;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;
use the_fuel_war\types\GameType;

class GameListForm extends SimpleForm
{

    private TaskScheduler $scheduler;
    private GameType $gameType;

    public function __construct(Player $player, GameType $gameType, TaskScheduler $scheduler) {
        $availableGamesAsButtons = [];
        $unavailableGamesAsButtons = [];
        $this->scheduler = $scheduler;
        $this->gameType = $gameType;


        foreach (GameStorage::findByGameType($gameType) as $game) {
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
                        $player->sendForm(new GameListForm($player, $this->gameType, $this->scheduler));
                    }
                );

            }
        }

        $gameAsButtonList = array_merge($availableGamesAsButtons, $availableGamesAsButtons);
        array_unshift($gameAsButtonList, new SimpleFormButton(
            TextFormat::BLUE . "更新",
            null,
            function (Player $player) {
                $player->sendForm(new GameListForm($player, $this->gameType, $this->scheduler));
            }
        ));
        array_unshift($gameAsButtonList, new SimpleFormButton(
            TextFormat::BLUE . "ランダムで参加",
            null,
            function (Player $player) {
                $result = JoinRandomGameService::execute($player->getName(), $this->gameType, $this->scheduler);
                if ($result) {
                    $playerData = PlayerDataDAO::findByName($player->getName());
                    if ($playerData === null) return;
                    if ($playerData->getBelongGameId() === null) return;

                    JoinGamePMMPService::execute($player, $playerData->getBelongGameId());
                } else {
                    $player->sendMessage("ゲームに参加できませんでした");
                }
            }
        ));

        parent::__construct("試合一覧", "タップで参加できます", $gameAsButtonList);
    }

    function onClickCloseButton(Player $player): void {
    }
}