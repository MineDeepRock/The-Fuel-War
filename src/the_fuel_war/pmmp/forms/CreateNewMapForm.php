<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\services\CreateNewMapService;
use the_fuel_war\pmmp\utilities\GetWorldNameList;
use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use pocketmine\Player;
use pocketmine\Server;

class CreateNewMapForm extends CustomForm
{
    private Input $inputNameElement;
    private Dropdown $selectWorldElement;

    public function __construct() {
        $worldNames = GetWorldNameList::execute();
        $this->inputNameElement = new Input("", "", "");
        $this->selectWorldElement = new Dropdown("ワールドを選択", $worldNames);
        parent::__construct(
            "マップを作成",
            [
                $this->inputNameElement,
                $this->selectWorldElement
            ]
        );
    }

    function onSubmit(Player $player): void {
        $mapName = $this->inputNameElement->getResult();
        $levelName = $this->selectWorldElement->getResult();
        $result = CreateNewMapService::execute($levelName, $mapName);

        if ($result) {
            $level = Server::getInstance()->getLevelByName($levelName);
            $player->teleport($level->getSpawnLocation());

            $player->sendForm(new MapSettingForm(MapDAO::findByName($mapName)));
        }
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MainMapForm());
    }
}