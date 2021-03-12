<?php


namespace the_fuel_ward\pmmp\forms;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\data\ItemDataOnMap;
use the_fuel_ward\models\Map;
use the_fuel_ward\pmmp\items\MedicineKitItem;
use the_fuel_ward\pmmp\slot_menus\ItemDataOnMapSettingSlotMenu;
use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\CustomForm;
use pocketmine\Player;
use slot_menu_system\SlotMenuSystem;

class EditItemDataOnMapForm extends CustomForm
{
    private Map $map;
    private ItemDataOnMap $itemDataOnMap;
    private Dropdown $itemNameElement;

    public function __construct(Map $map, ItemDataOnMap $itemDataOnMap) {
        $this->map = $map;
        $this->itemDataOnMap = $itemDataOnMap;

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
        $itemDataOnMapList = [];
        foreach ($this->map->getItemDataOnMapList() as $itemDataOnMap) {
            if ($itemDataOnMap->getVector()->equals($this->itemDataOnMap->getVector())) {
                $itemDataOnMapList[] = new ItemDataOnMap($itemName, $itemDataOnMap->getVector());
            } else {
                $itemDataOnMapList[] = $itemDataOnMap;
            }
        }


        MapDAO::updatePartOfMap($this->map->getName(), ["item_data_list" => $itemDataOnMapList]);
        $player->sendForm(new ItemDataOnMapListForm(MapDAO::findByName($this->map->getName())));
    }

    function onClickCloseButton(Player $player): void {
        SlotMenuSystem::send($player, new ItemDataOnMapSettingSlotMenu($this->map, $this->itemDataOnMap));
    }
}