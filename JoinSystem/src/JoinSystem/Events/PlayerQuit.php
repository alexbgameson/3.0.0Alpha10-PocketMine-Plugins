<?php
namespace JoinSystem\Events;

use JoinSystem\JoinSystem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\utils\Config;

class PlayerQuit implements Listener{
	
	public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		
		$quitmsg = JoinSystem::getInstance()->getConfig()->get("QuitMsg");
		$quitmsg = str_replace("{player}", $name, $quitmsg);
		
		$event->setQuitMessage($quitmsg);
		}
}