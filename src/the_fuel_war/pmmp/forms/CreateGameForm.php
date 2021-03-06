<?php


namespace the_fuel_war\pmmp\forms;


use form_builder\models\custom_form_elements\Label;
use form_builder\models\custom_form_elements\Toggle;
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
use the_fuel_war\storages\UsingMapNameList;
use the_fuel_war\types\GameType;

class CreateGameForm extends CustomForm
{
    private TaskScheduler $scheduler;

    private Dropdown $mapNameElement;
    private Dropdown $gameTypeElement;
    private Slider $maxPlayersElement;
    private Toggle $canRespawnElement;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;

        $mapNames = [];
        foreach (MapDAO::all() as $map) {
            if (!UsingMapNameList::isExist($map->getName())) {
                $numberOfTank = $map->getFuelTankMapDataList();
                $mapNames[] = "name:{$map->getName()},tank:{$numberOfTank}";
            }
        }

        $this->mapNameElement = new Dropdown("マップ", $mapNames);
        $this->gameTypeElement = new Dropdown("チーム人数", [
            strval(GameType::Unspecified()) . "マップに合わせて調整されます",
            strval(GameType::Solo()),
            strval(GameType::TwoPlayers()),
        ]);
        $this->maxPlayersElement = new Slider("最大プレイヤー数", 2, 10, 8);
        $this->canRespawnElement = new Toggle("リスポーン", true);

        parent::__construct(
            "ゲームを作成",
            [
                $this->mapNameElement,
                $this->gameTypeElement,
                $this->maxPlayersElement,
                $this->canRespawnElement,
            ]
        );
    }

    function onSubmit(Player $player): void {
        $mapName = $this->mapNameElement->getResult();
        $gameType = GameType::fromString($this->gameTypeElement->getResult());
        $maxPlayers = intval($this->maxPlayersElement->getResult());
        $canRespawn = $this->canRespawnElement->getResult();

        $result = CreateGameService::execute($player->getName(), $mapName, $gameType, $maxPlayers, $canRespawn, $this->scheduler);
        if ($result) {
            $player->sendMessage("ゲームを作成しました");

            $game = GameStorage::findOwnerName($player->getName());
            //オーナーも参加させる
            JoinGameService::execute($game->getGameId(), $game->getGameOwnerName(), $this->scheduler);
            JoinGamePMMPService::execute($player, $game->getGameId());
        } else {
            $player->sendMessage("ゲームを作成できませんでした");
        }
    }

    function onClickCloseButton(Player $player): void { }
}