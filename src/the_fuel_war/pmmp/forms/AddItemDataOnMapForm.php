<?php


namespace the_fuel_war\pmmp\forms;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\data\ItemDataOnMap;
use the_fuel_war\models\Map;
use the_fuel_war\pmmp\items\MedicineKitItem;
use the_fuel_war\pmmp\slot_menus\AddItemDataOnMapSlotMenu;
use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\CustomForm;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class AddItemDataOnMapForm extends CustomForm
{
    private Map $map;
    private Vector3 $vector;
    private Dropdown $itemNameElement;

    public function __construct(Map $map, Vector3 $vector) {
        $this->map = $map;
        $this->vector = $vector;

        //TODO:一覧用のクラスを用意する
        $this->itemNameElement = new Dropdown(
            "アイテム一覧",
            [
                MedicineKitItem::Name

            ],
            0
        );
        parent::__construct("銃を選択", [
            $this->itemNameElement,
        ]);
    }


    function onSubmit(Player $player): void {
        $itemName = $this->itemNameElement->getResult();
        $itemDataOnMapList = $this->map->getItemDataOnMapList();
        $itemDataOnMapList[] = new ItemDataOnMap($itemName, $this->vector);

        MapDAO::updatePartOfMap($this->map->getName(), ["item_data_list" => $itemDataOnMapList]);
        $player->sendForm(new ItemDataOnMapListForm(MapDAO::findByName($this->map->getName())));
    }

    function onClickCloseButton(Player $player): void {
        SlotMenuSystem::send($player, new AddItemDataOnMapSlotMenu($this->map));
    }
}