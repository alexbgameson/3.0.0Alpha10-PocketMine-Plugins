<?php

namespace BoxOfDevs\BPad;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\particle\Particle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\block\Block;

class Main extends PluginBase implements Listener {
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this ,$this);
		$this->saveDefaultConfig();
		$this->config = $this->getConfig();
		$this->getLogger()->info(C::GREEN."Enabled!");
	}
	
	public function onMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$level = $player->getLevel();
		$block = $level->getBlock($player->getSide(0));
		if($block->getID() == $this->config->get('Block')){
			$direction = $player->getDirectionVector();
			$dx = $direction->getX();
			$dz = $direction->getZ();
			if($this->config->get("Particle") == "true"){
				$level->addParticle(new FlameParticle($player));
				$level->addParticle(new FlameParticle(new Vector3($x-0.3, $y, $z)));
				$level->addParticle(new FlameParticle(new Vector3($x, $y, $z-0.3)));
				$level->addParticle(new FlameParticle(new Vector3($x+0.3, $y, $z)));
				$level->addParticle(new FlameParticle(new Vector3($x, $y, $z+0.3)));
			}
			$player->knockBack($player, 0, $dx, $dz, $this->config->get('BoostPower'));
		}
	}
	
	public function onDisable(){
		$this->getLogger()->info(C::DARK_RED."Disabled!");
	}
}
