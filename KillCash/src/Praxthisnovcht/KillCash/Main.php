<?php
# Authorization to Topic

namespace Praxthisnovcht\KillCash;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\Server;

class Main extends PluginBase implements Listener{

	public $economy = false;

	public function onEnable(){
		@mkdir($this->getDataFolder());

		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
			"enable" => "true",
			"economy-plugin" => "EconomyAPI",
			"money"  => 100,
			"message" => "§aYou just earned {money}"
        ));
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if($this->config->get("economy-plugin") == "EconomyAPI"){
        	if(is_dir($this->getServer()->getPluginPath()."EconomyAPI")){
				$this->getLogger()->info(TextFormat::GREEN."KillCash successful loaded with Economy!API");
				$this->economy = true;
			}else{
				$this->getLogger()->info(TextFormat::RED."KillCash not loaded, I can't find EconomyAPI");
				$this->economy = false;
			}   }if($this->config->get("economy-plugin") == "EconomyMaster"){
        	if(is_dir($this->getServer()->getPluginPath()."EconomyMaster")){
				$this->getLogger()->info(TextFormat::GREEN."KillCash successful loaded with EconomyMaster!");
				$this->economy = true;
			}else{
				$this->getLogger()->info(TextFormat::RED."KillCash not loaded, I can't find EconomyMaster");
				$this->economy = false;
		    }
        }if($this->config->get("economy-plugin") == "PocketMoney"){
        	if(is_dir($this->getServer()->getPluginPath()."PocketMoney")){
				$this->getLogger()->info(TextFormat::GREEN."KillCash successful loaded with PocketMoney!");
				$this->economy = true;
			}else{
				$this->getLogger()->info(TextFormat::RED."KillCash not loaded, I can't find PocketMoney");
				$this->economy = false;
		    }
        }if($this->config->get("economy-plugin") == "MassiveEconomyAPI"){
        	if(is_dir($this->getServer()->getPluginPath()."MassiveEconomyAPI")){
				$this->getLogger()->info(TextFormat::GREEN."KillCash successful loaded with MassiveEconomyAPI!");
				$this->economy = true;
			}else{
				$this->getLogger()->info(TextFormat::RED."KillCash not loaded, I can't find MassiveEconomyAPI");
				$this->economy = false;
        }
	}
}
	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED."KillCash unloaded!");
	}

	public function onPlayerDeath(PlayerDeathEvent $event){
		if($this->economy == true && $this->config->get("enable") == "true"){
       		$entity = $event->getEntity();
        	$cause = $entity->getLastDamageCause();
			if($cause instanceof EntityDamageByEntityEvent) {
				$killer = $cause->getDamager()->getPlayer();
				if($this->config->get("economy-plugin") == "EconomyAPI") {
					$msg = str_replace("{money}", $this->config->get("money"), $this->config->get("message"));
					$killer->sendMessage("$msg");
					$this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($killer->getName(), $this->config->get("money"));
					return true;
				}
				if($this->config->get("economy-plugin") == "PocketMoney") {
					$msg = str_replace("{money}", $this->config->get("money"), $this->config->get("message"));
					$killer->sendMessage("$msg");
					$this->getServer()->getPluginManager()->getPlugin("PocketMoney")->payMoney($killer->getName(), $this->config->get("money"));
					return true;
				}
				if($this->config->get("economy-plugin") == "MassiveEconomyAPI") {
					$msg = str_replace("{money}", $this->config->get("money"), $this->config->get("message"));
					$killer->sendMessage("$msg");
					$this->getServer()->getPluginManager()->getPlugin("MassiveEconomyAPI")->payPlayer($killer->getName(), $this->config->get("money"));
					return true;
				}
			if($this->config->get("economy-plugin") == "EconomyMaster ") {
					$msg = str_replace("{money}", $this->config->get("money"), $this->config->get("message"));
					$killer->sendMessage("$msg");
					$this->getServer()->getPluginManager()->getPlugin("EconomyMaster")->PayPlayerMoney($killer->getName(), $this->config->get("money"));
					return true;
					}

			}else{
				return true;
			}
		}
	}
}
