<?php


namespace the_fuel_ward\pmmp\forms;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\services\CreateGameService;
use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Slider;
use form_builder\models\CustomForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

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
        $this->maxPlayersElement = new Slider("最大プレイヤー数", 3, 10, 6);

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
        } else {
            $player->sendMessage("ゲームを作成できませんでした");
        }
    }

    function onClickCloseButton(Player $player): void { }
}