<?php


namespace the_fuel_ward\pmmp\forms;


use the_fuel_ward\pmmp\services\StartGamePMMPService;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GameSettingForm extends SimpleForm
{

    public function __construct(TaskScheduler $taskScheduler) {
        parent::__construct("試合の設定", "", [
            new SimpleFormButton("開始", null, function (Player $player) use ($taskScheduler) {
                $result = StartGamePMMPService::execute($player, $taskScheduler);
                if (!$result) {
                    $player->sendMessage("試合を開始できませんでした");
                }
            })
        ]);
    }

    function onClickCloseButton(Player $player): void {
    }
}