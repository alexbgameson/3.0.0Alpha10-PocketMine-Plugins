<?php
namespace enderchest;

use pocketmine\Player;

use pocketmine\block\BlockFactory;
use pocketmine\item\Item;
use pocketmine\tile\Tile;

use pocketmine\plugin\PluginBase;

/*
 * Developed by TheAz928 (Az928)
 * CopyRight (C) @TheAz928, All
 * Rights (R) reserved. This software
 * Is distributed under GNU General
 * Public License v3.0.0 and later
 * You can modify the code by giving
 * The original author (TheAz928) Credits
 * And you cannot take credits yourself
 */

class Loader extends PluginBase{
	
	/** @var data_base */
	private $data_base;
	
	/** @var instance */
	private static $instance;
	
	/** @var inv */
	private $inv = []; 
	
	public function onLoad(){
	    self::$instance = $this;
	    BlockFactory::registerBlock(new EnderChest(), true);
	    Tile::registerTile(EnderChestTile::class, true);
	    Item::addCreativeItem(Item::get(130, 0));
	    $this->data_base = new DataBase($this->getServer()->getDataPath()."players/chestdata/");
	}
	
	/**
	 * @param Player $player
	 */
	
	public function getInventory(Player $player): EnderChestInventory{
	    if(isset($this->inv[$player->getName()])){
		   return $this->inv[$player->getName()];
		 }
	    $this->inv[$player->getName()] = new EnderChestInventory($player);
	return $this->getInventory($player);
	}
	
	/**
	 * @return $this
	 */
	
	public static function getInstance(): Loader{
	    return self::$instance;
	}
}