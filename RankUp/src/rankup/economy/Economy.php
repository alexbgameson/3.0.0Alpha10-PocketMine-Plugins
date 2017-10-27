<?php
namespace rankup\economy;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

class Economy extends BaseEconomy{
    public function give($amt, Player $player){
        if(!$this->checkReady()) return false;
        return ($this->getAPI()->addMoney($player, $amt) === 1);
    }
    public function take($amt, Player $player){
        if(!$this->checkReady()) return false;
        return ($this->getAPI()->reduceMoney($player, $amt) === 1);
    }
    public function setBal($amt, Player $player){
        if(!$this->checkReady()) return false;
        return ($this->getAPI()->setMoney($player, $amt) === 1);
    }
    public function getBal(Player $player){
        if(!$this->checkReady()) return false;
        //TODO there is probably a better way to do this
        $money = $this->getAPI()->getAllMoney();
        if(isset($money["money"][strtolower($player->getName())])){
            return $money["money"][strtolower($player->getName())];
        }
        else{
            return false;
        }
    }

    /**
     * @return Plugin
     */
    public function getAPI(){
        return $this->getPlugin()->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    }
    public function isReady(){
        return ($this->getAPI() instanceof PluginBase);
    }
    public function getName(){
        return "EconomyS by onebone";
    }
}