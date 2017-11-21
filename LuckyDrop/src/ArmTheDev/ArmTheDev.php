<?php
namespace ArmTheDev;

use pocketmine\{Server, Player};
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

use pocketmine\event\block\BlockBreakEvent;

use pocketmine\utils\TextFormat as C;

use pocketmine\item\Item;

use pocketmine\math\Vector3;

class ArmTheDev extends PluginBase implements Listener{
	
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this ,$this);
	}

public function onBreak(BlockBreakEvent $event){
	$block = $event->getBlock();
	$id = $block->getId();
	$level = $block->getLevel();
	if($id == 18){
		$drop = rand(1,30);
		switch($drop){
			case 1: //Adding new cases later
			  $level->dropItem(new Vector3($block->getX() + 0.5, $block->getY() + 1, $block->getZ() + 0.5), Item::get(Item::DIAMOND, 0, 1));
			break;
			}
		}
		}
}
