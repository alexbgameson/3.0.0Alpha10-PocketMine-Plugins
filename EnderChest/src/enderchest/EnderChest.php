<?php
namespace enderchest;

use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\block\BlockFactory;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

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

class EnderChest extends Transparent{

	protected $id = 130;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 22.5;
	}
	
	public function getBlastResistance(): float{
	    return 3000;
	}
	
	public function getLightLevel(): Int{
	    return 7;
	}

	public function getName() : string{
		return "Ender Chest";
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	protected function recalculateBoundingBox() : ?AxisAlignedBB{
		return new AxisAlignedBB(
			$this->x + 0.025,
			$this->y,
			$this->z + 0.025,
			$this->x + 0.975,
			$this->y + 0.95,
			$this->z + 0.975
		);
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		$tile = Tile::createTile("EnderChestTile", $this->getLevel(), EnderChestTile::createNBT($this, $face, $item, $player));
   return true;
	}

	public function onBreak(Item $item, Player $player = null) : bool{
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof EnderChestTile){
			$tile->close();
		}
		$this->getLevel()->setBlock($this, BlockFactory::get(Block::AIR), true, true);
	return true;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		  if($player instanceof Player){
			 $tile = $this->getLevel()->getTile($this);
			 if($tile == null){
				$tile = Tile::createTile("EnderChestTile", $this->getLevel(), EnderChestTile::createNBT($this));
			 }
			 if(!$this->getSide(Vector3::SIDE_UP)->isTransparent() or $tile instanceof EnderChestTile == false){
				return true;
			 }
			 $inv = Loader::getInstance()->getInventory($player);
			 $inv->updateHolderPosition($tile); 
			 $player->addWindow($inv);
		}
	return true;
	}

	public function getFuelTime() : int{
		return 0;
	}
	
	public function getDrops(Item $item): array{
	    if($item->isPickaxe() >= Tool::TIER_WOODEN){
		   return [Item::get(Item::OBSIDIAN, 0, 8)];
		}
	}
}