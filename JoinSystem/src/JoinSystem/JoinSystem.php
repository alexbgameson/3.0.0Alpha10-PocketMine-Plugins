<?php

namespace JoinSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\lang\BaseLang;

#Own

use JoinSystem\Commands\CommandHub;
use JoinSystem\Events\{PlayerJoin,PlayerQuit};

class JoinSystem extends PluginBase implements Listener{
	
	const PREFIX = "§7[§2JoinSystem§7]";
	
	public static $instance;
	
	public function onEnable(){
		$this->getLogger()->info(self::PREFIX . " by §6McpeBooster§7!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		//English is only supportet at the moment
		$lang = "eng";
		$this->getLogger()->info(self::PREFIX . " Language: ".$lang);
		
		self::$instance = $this;
		$this->registerCommands();
		$this->registerEvents();
		
		if($this->getConfig()->get("Lobby") == "debug123"){
			$plugin = $this->getServer()->getPluginManager()->getPlugin("JoinSystem");
			$this->getLogger()->emergency("######################################################");
			$this->getLogger()->emergency("Please change the Lobby world in the config.yml!!!");
			$this->getLogger()->emergency("######################################################");
			$this->getServer()->getPluginManager()->disablePlugin($plugin);
			return;
		}
		
		$this->getServer()->loadLevel($this->getConfig()->get("Lobby"));
	}
	
	public static function getInstance(){
		return self::$instance;
	}
	
	private function registerCommands(){
		$this->getServer()->getCommandMap()->register("hub", new CommandHub());
	}
	
	private function registerEvents(){
		$this->getServer()->getPluginManager()->registerEvents(new PlayerJoin(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerQuit(), $this);
	}
}
