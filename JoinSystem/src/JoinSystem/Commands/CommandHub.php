<?php
namespace JoinSystem\Commands;

use JoinSystem\JoinSystem;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\Player;

class CommandHub extends Command{
	public function __construct(){
		parent::__construct("hub", "", null);
		}
		
		public function execute(CommandSender $sender, $label, array $args){
			if($sender instanceof Player){
				$player = $sender;
				$lobby = JoinSystem::getInstance()->getConfig()->get("Lobby");
				$level = JoinSystem::getInstance()->getServer()->getLevelByName($lobby);
				$spawn = $level->getSafeSpawn();
				$player->teleport($spawn, 0, 0);
				$player->addTitle("§2Willkommen", "§4".$player->getName());
				}else{
					$sender->sendMessage(JoinSystem::PREFIX . " by §6McpeBooster§7!");
				}
		}
}