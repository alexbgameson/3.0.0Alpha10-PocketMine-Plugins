<?php
namespace rankup\economy;

use EssentialsPEconomy\EssentialsPEconomy;
use EssentialsPEconomy\Providers\BaseEconomyProvider;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class EssentialsEconomy extends BaseEconomy{
    public function give($amt, Player $player){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->addToBalance($player, $amt);
    }
    public function take($amt, Player $player){
        if(!$this->checkReady()) return false;

        return $this->getAPI()->subtractFromBalance($player, $amt);
    }
    public function setBal($amt, Player $player){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->setBalance($player, $amt);
    }
    public function getBal(Player $player){
        return $this->getAPI()->getBalance($player);
    }
    /**
     * @return BaseEconomyProvider
     */
    public function getAPI(){
        return $this->getPlugin()->getServer()->getPluginManager()->getPlugin("EssentialsPEconomy")->getProvider();
    }
    public function isReady(){
        return $this->getPlugin()->getServer()->getPluginManager()->getPlugin("EssentialsPEconomy") instanceof PluginBase;
    }
    public function getName(){
        return "EssentialsPE by LegendsOfMCPE";
    }
}
