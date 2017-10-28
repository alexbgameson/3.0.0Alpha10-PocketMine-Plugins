<?php
namespace CustomHelp\Commands;

use CustomHelp\CustomHelp;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class CommandHelp extends PluginCommand {
	public function __construct(CustomHelp $plugin) {
		parent::__construct("help", $plugin);
		$this->setDescription("Shows the help menu");
		$this->setUsage("/help [page]");
	}
	public function execute(CommandSender $sender, $label, array $args) {
		$sites = $this->getPlugin()->getConfig()->get("CountHelpSites", []);
		$cpers = $this->getPlugin()->getConfig()->get("CommandsPerSite", []);
		if(!empty($args[0])) {
			if(in_array($args[0], $sites)) {
				$sender->sendMessage("§7-----[CustomHelp]-----");
				foreach($cpers as $cp) {
					$cmd = $this->getPlugin()->getConfig()->getNested($args[0].".line".$cp);
					$cmd = str_replace("ß", "§", $cmd);
					$cmd = str_replace("&", "§", $cmd);
					$sender->sendMessage($cmd);
				}
				return true;
			}
		}else{
			$sender->sendMessage("§7-----[CustomHelp]-----");
			foreach($cpers as $cp) {
				$cmd = $this->getPlugin()->getConfig()->getNested("1.line".$cp);
				$cmd = str_replace("ß", "§", $cmd);
				$cmd = str_replace("&", "§", $cmd);
				$sender->sendMessage($cmd);
			}
		}
		return true;
	}
}
