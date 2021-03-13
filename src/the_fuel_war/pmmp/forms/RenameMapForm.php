<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\models\Map;
use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use pocketmine\Player;

class RenameMapForm extends CustomForm
{
    private Map $map;
    private Input $nameInputElement;

    public function __construct(Map $map) {
        $this->map = $map;
        $this->nameInputElement = new Input("", "", $map->getName());
        parent::__construct($map->getName() . "の名前を変更する", [
            $this->nameInputElement,
        ]);
    }

    function onSubmit(Player $player): void {
        MapDAO::updatePartOfMap($this->map->getName(),["name" => $this->nameInputElement->getResult()]);

        $updatedMap = MapDAO::findByName($this->map->getName());
        $player->sendForm(new MapSettingForm($updatedMap));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }
}