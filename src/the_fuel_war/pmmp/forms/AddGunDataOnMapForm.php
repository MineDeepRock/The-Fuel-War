<?php


namespace the_fuel_ward\pmmp\forms;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\data\GunDataOnMap;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\slot_menus\AddGunDataOnMapSlotMenu;
use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\CustomForm;
use gun_system\GunSystem;
use gun_system\model\Gun;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class AddGunDataOnMapForm extends CustomForm
{
    private Map $map;
    private Vector3 $vector;
    private Dropdown $gunNameElement;

    public function __construct(Map $map, Vector3 $vector) {
        $this->map = $map;
        $this->vector = $vector;

        $gunNames = array_map(function (Gun $gun) {
            return $gun->getName();
        }, GunSystem::loadAllGuns());

        $this->gunNameElement = new Dropdown("銃一覧", $gunNames, 0);
        parent::__construct("銃を選択", [
            $this->gunNameElement,
        ]);
    }


    function onSubmit(Player $player): void {
        $gunName = $this->gunNameElement->getResult();
        $gunDataOnMapList = $this->map->getGunDataOnMapList();
        $gunDataOnMapList[] = new GunDataOnMap($gunName, $this->vector);

        MapDAO::updatePartOfMap($this->map->getName(), ["gun_data_list" => $gunDataOnMapList]);
        $player->sendForm(new GunDataOnMapListForm(MapDAO::findByName($this->map->getName())));
    }

    function onClickCloseButton(Player $player): void {
        SlotMenuSystem::send($player, new AddGunDataOnMapSlotMenu($this->map));
    }
}