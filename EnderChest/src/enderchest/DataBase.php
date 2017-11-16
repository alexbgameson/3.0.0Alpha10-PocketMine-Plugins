<?php
namespace enderchest;

use pocketmine\Player;

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

// ToDo: Add SQL and MySQL support

class DataBase{
	
	/** @var path */
	private static $path = '';
	
	public function __construct(string $path){
	    if(!is_dir($path)){
		   @mkdir($path);
		 }
		 self::$path = $path;
	}
	
	/**
	 * @param Player $player
	 * @param EnderChestInventory $inv
	 */
	
	public static function saveInventoryContents(Player $player, EnderChestInventory $inv): void{
	    $contents = $inv->getContents();
	    $data = serialize($contents);
	    if(file_exists(self::$path.$player->getName().".dat")){
		   unlink(self::$path.$player->getName().".dat");
		 }
		 file_put_contents(self::$path.$player->getName().".dat", $data);
	}
	
	/**
	 * @param Player $player
	 * @return Item[]
	 */
	
	public static function getInventoryContents(Player $player): array{
	    if(!file_exists(self::$path.$player->getName().".dat")){
		   return [];
		 }else{
		  return unserialize(file_get_contents(self::$path.$player->getName().".dat"));
		}
	}
}