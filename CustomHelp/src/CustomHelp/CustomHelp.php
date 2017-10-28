<?php
namespace CustomHelp;

use pocketmine\plugin\PluginBase;

use CustomHelp\Commands\CommandHelp;

class CustomHelp extends PluginBase {
	
	public $prefix = "§7[§2CustomHelp§7]";
	
	public function onEnable() {
		$this->getLogger()->info($this->prefix . " by §6McpeBooster§7!");
		$this->saveDefaultConfig();
		//English is only supportet at the moment
		$lang = "eng";
		$this->getLogger()->info($this->prefix . " Language: ".$lang);
		$this->registerCommands();
	}
	private function registerCommands(){
		$map = $this->getServer()->getCommandMap();
		$old = $map->getCommand("help");
		$old->setLabel("help_disabled");
		$old->unregister($map);
		$new = new CommandHelp($this);
		$map->register($this->getName(), $new, "help");
	}
}
