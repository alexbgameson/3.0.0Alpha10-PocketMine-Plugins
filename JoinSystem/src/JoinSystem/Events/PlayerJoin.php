<?php
namespace JoinSystem\Events;

use JoinSystem\JoinSystem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\Config;

class PlayerJoin implements Listener{
	function __construct() {
		
	}
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		
		$joinmsg = JoinSystem::getInstance()->getConfig()->get("JoinMsg");
		$joinmsg = str_replace("{player}", $name, $joinmsg);
		
		$event->setJoinMessage($joinmsg);
		
		$player->setGamemode(2);
		$player->setHealth(20);
		$player->setFood(20);
		$lobby = JoinSystem::getInstance()->getConfig()->get("Lobby");
		$level = JoinSystem::getInstance()->getServer()->getLevelByName($lobby);
		$spawn = $level->getSafeSpawn();
		$player->teleport($spawn, 0, 0);
		//$player->addTitle("§2Willkommen", "§4".$player->getName());
		}
}
