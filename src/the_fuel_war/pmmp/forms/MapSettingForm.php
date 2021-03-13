<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\models\Map;
use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use slot_menu_system\SlotMenuSystem;

class MapSettingForm extends SimpleForm
{

    public function __construct(Map $map) {
        $buttons = [
            new SimpleFormButton(
                "名前の変更",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new RenameMapForm($map));
                }
            ),
            new SimpleFormButton(
                "燃料タンクの変更",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new FuelTankListForm($map));
                }
            ),
            new SimpleFormButton(
                "燃料のスポーン位置の変更",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new FuelSpawnVectorSettingForm($map));
                }
            ),
            new SimpleFormButton(
                "アイテムのスポーン位置の変更",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new ItemDataOnMapListForm($map));
                }
            ),
            new SimpleFormButton(
                "銃のスポーン位置の変更",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new GunDataOnMapListForm($map));
                }
            ),
            new SimpleFormButton(
                "輸血パックのスポーン位置の変更",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new BloodPackSpawnVectorListForm($map));
                }
            ),
            new SimpleFormButton(
                TextFormat::RED . "マップの削除",
                null,
                function (Player $player) use ($map) {
                    $player->sendForm(new ConfirmDeleteMapForm($map));
                }
            ),
        ];
        parent::__construct($map->getName(), "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}