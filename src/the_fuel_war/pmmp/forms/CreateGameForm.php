<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\pmmp\services\JoinGamePMMPService;
use the_fuel_war\services\CreateGameService;
use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Slider;
use form_builder\models\CustomForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use the_fuel_war\services\JoinGameService;
use the_fuel_war\storages\GameStorage;

class CreateGameForm extends CustomForm
{
    private TaskScheduler $scheduler;

    private Dropdown $mapNameElement;
    private Slider $maxPlayersElement;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;

        $mapNames = [];
        foreach (MapDAO::all() as $map) {
            $mapNames[] = $map->getName();
        }

        $this->mapNameElement = new Dropdown("マップ", $mapNames);
        $this->maxPlayersElement = new Slider("最大プレイヤー数", 2, 10, 8);

        parent::__construct(
            "ゲームを作成",
            [
                $this->mapNameElement,
                $this->maxPlayersElement,
            ]
        );
    }

    function onSubmit(Player $player): void {
        $mapName = $this->mapNameElement->getResult();
        $maxPlayers = intval($this->maxPlayersElement->getResult());

        $result = CreateGameService::execute($player->getName(), $mapName, $maxPlayers, $this->scheduler);
        if ($result) {
            $player->sendMessage("ゲームを作成しました");

            $game = GameStorage::findOwnerName($player->getName());
            //オーナーも参加させる
            JoinGameService::execute($game->getGameId(), $game->getGameOwnerName(), $this->scheduler);
            JoinGamePMMPService::execute($player, $game->getGameId(), $this->scheduler);
        } else {
            $player->sendMessage("ゲームを作成できませんでした");
        }
    }

    function onClickCloseButton(Player $player): void { }
}