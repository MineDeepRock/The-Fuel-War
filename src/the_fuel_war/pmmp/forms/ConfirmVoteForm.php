<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\pmmp\entities\DyingPlayerEntity;
use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use pocketmine\Player;

class ConfirmVoteForm extends ModalForm
{

    private DyingPlayerEntity $dyingPlayerEntity;

    public function __construct(DyingPlayerEntity $dyingPlayerEntity) {
        $this->dyingPlayerEntity = $dyingPlayerEntity;
        parent::__construct(
            $dyingPlayerEntity->getOwner()->getName() . "に投票しますか",
            "",
            new ModalFormButton("投票する"),
            new ModalFormButton("キャンセル"),
        );
    }

    function onClickCloseButton(Player $player): void { }

    public function onClickButton1(Player $player): void {
        $this->dyingPlayerEntity->vote($player->getName());
    }

    public function onClickButton2(Player $player): void { }
}