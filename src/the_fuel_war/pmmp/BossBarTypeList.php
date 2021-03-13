<?php


namespace the_fuel_war\pmmp;


use bossbar_system\model\BossBarType;

class BossBarTypeList
{
    static function GameTimer(): BossBarType {
        return new BossBarType("GameTimer");
    }

    static function Transform(): BossBarType {
        return new BossBarType("Transform");
    }
}