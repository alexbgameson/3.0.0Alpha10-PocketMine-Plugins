<?php
namespace enderchest;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Nameable;
use pocketmine\tile\Spawnable;

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

class EnderChestTile extends Spawnable implements Nameable {

	/**
	 * @return string
	 */
	
	public function getDefaultName(): string{
	    return "Ender Chest";
	}
	
	/**
	 * @return string
	 */
	
	public function getName() : string{
		return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "Ender Chest";
	}

	/**
	 * @return bool
	 */
	
	public function hasName(): bool{
		return isset($this->namedtag->CustomName);
	}

	/**
	 * @param string $str
	 */
	
	public function setName(string $name){
		if($name == ""){
			unset($this->namedtag->CustomName);
		return;
		}
		$this->namedtag->CustomName = new StringTag("CustomName", $name);
	}

	/**
	 * @void addAdditionalSpawnData
	 * @param CompoundTag $nbt
	 */
	
	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		if($this->hasName()){
			$nbt->CustomName = $this->namedtag->CustomName;
		}
	}
}