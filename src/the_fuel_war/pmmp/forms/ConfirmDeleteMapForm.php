<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\models\Map;
use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use pocketmine\Player;

class ConfirmDeleteMapForm extends ModalForm
{
    private Map $map;

    public function __construct(Map $map) {
        $this->map = $map;
        parent::__construct("本当に{$map->getName()}を削除しますか", "", new ModalFormButton("削除"), new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }

    public function onClickButton1(Player $player): void {
        MapDAO::delete($this->map->getName());
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new MapSettingForm($this->map));
    }
}