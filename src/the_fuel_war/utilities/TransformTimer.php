<?php


namespace the_fuel_war\utilities;


use bossbar_system\BossBar;
use the_fuel_war\pmmp\BossBarTypeList;
use the_fuel_war\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_war\pmmp\services\TransformToPlayerPMMPService;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\storages\PlayerStatusStorage;

class TransformTimer extends Timer
{
    private string $playerName;
    private bool $isProgress = false;

    public function __construct(string $playerName, TaskScheduler $scheduler) {
        $this->playerName = $playerName;
        parent::__construct(10, 0, $scheduler);
    }

    public function start(): void {
        $this->isProgress = true;

        $player = Server::getInstance()->getPlayer($this->playerName);
        if ($player === null) return;

        $bossBar = new BossBar($player, BossBarTypeList::Transform(), "Transform", 1);
        $bossBar->send();

        $this->updateScoreboard();

        parent::start();
    }

    public function onUpdatedTimer(): void {
        $player = Server::getInstance()->getPlayer($this->playerName);
        if ($player === null) return;

        $bossBar = BossBar::findByType($player, BossBarTypeList::Transform());
        if ($bossBar === null) return;//TODO:error

        if (($this->getTimeLeft() / $this->getInitialTime()) === 1) {
            $bossBar->updatePercentage(0.01);

        } else {
            $bossBar->updatePercentage(1 - ($this->getTimeLeft() / $this->getInitialTime()));

        }
    }

    public function onStoppedTimer(): void {
        $this->onFinishedTimer();
    }

    public function onFinishedTimer(): void {
        $this->isProgress = false;

        $player = Server::getInstance()->getPlayer($this->playerName);
        if ($player === null) return;

        TransformToPlayerPMMPService::execute($player);

        $bossBar = BossBar::findByType($player, BossBarTypeList::Transform());
        if ($bossBar !== null) $bossBar->remove();
        $this->updateScoreboard();
    }

    /**
     * @return bool
     */
    public function isProgress(): bool {
        return $this->isProgress;
    }

    private function updateScoreboard() {
        $playerStatus = PlayerStatusStorage::findByName($this->playerName);
        if ($playerStatus === null) return;

        $game = GameStorage::findById($playerStatus->getBelongGameId());
        if ($game === null) return;

        //スコアボード更新
        $gamePlayers = [];
        foreach ($game->getPlayerNameList() as $name) {
            $gamePlayer = Server::getInstance()->getPlayer($name);
            if ($gamePlayer === null) return;
            $gamePlayers[] = $gamePlayer;
        }
        OnGameScoreboard::update($gamePlayers, $game);
    }
}