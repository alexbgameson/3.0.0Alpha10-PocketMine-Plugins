<?php

namespace SoupFFA;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\tile\Sign;
use pocketmine\lang\BaseLang;

use pocketmine\command\{Command, CommandSender};

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};

class SoupFFA extends PluginBase implements Listener{
	
	public $prefix = "§7[§2SoupFFA§7]";

	public function onEnable(){
		$this->getLogger()->info($this->prefix . " by §6McpeBooster§7!");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->saveDefaultConfig();
		
		$lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
		$this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");
		
		$this->getLogger()->info($this->prefix . " Language: ".$lang);
		
		//Check for Update
		$this->checkUpdate();
		
		
		if($this->getConfig()->get("arena") == "debug123"){
			$plugin = $this->getServer()->getPluginManager()->getPlugin("SoupFFA");
			$this->getLogger()->emergency("######################################################");
			$this->getLogger()->emergency($this->getLanguage()->get("setting.world.change"));
			$this->getLogger()->emergency("######################################################");
			$this->getServer()->getPluginManager()->disablePlugin($plugin);
			return;
		}
		
		$this->getServer()->loadLevel($this->getConfig()->get("arena"));
		
	}
	
	/**
	 * @api
	 * @return BaseLang
	 */
	 
	public function getLanguage() : BaseLang {
		return $this->baseLang;
	}
	
	public function checkUpdate(){
		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
				),
			);
		
		$datei = file_get_contents("https://raw.githubusercontent.com/McpeBooster/SoupFFA-McpeBooster/master/plugin.yml", false, stream_context_create($arrContextOptions));
		if(!$datei) return false;
		
		$datei = str_replace("\n", "", $datei);
		$newversion = explode("version: ", $datei);
		$newversion = explode("api: ", $newversion[1]);
		$newversion = $newversion[0];
		//var_dump($newversion);
		
		$plugin = $this->getServer()->getPluginManager()->getPlugin("SoupFFA");
		$version = $plugin->getDescription()->getVersion();
		//var_dump($version);
		if(!($version === $newversion)){
			$update = false;
			if(intval($version[0]) < intval($newversion[0])){
				$update = true;
			}elseif(intval($version[0]) === intval($newversion[0])){
				if(intval($version[1]) < intval($newversion[1])){
					$update = true;
				}elseif(intval($version[1]) === intval($newversion[1])){
					if(intval($version[2]) < intval($newversion[2])){
						$update = true;
					}
				}
			}
			
			if($update){
				$this->getLogger()->info("§aNew Update available!");
				$this->getLogger()->info("§7Local Version: §6" . $version);
				$this->getLogger()->info("§7Newest Version: §6" . $newversion);
				$this->getLogger()->info("§aUpdate your Plugin by downloading the new source at §7https://github.com/McpeBooster/SoupFFA-McpeBooster/");
				$this->getLogger()->info("§aor get the newest .phar at §7http://McpeBooster.tk/plugins/");
				return true;
			}
		}
		
		return false;
	}
	
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();
		if($item->getId() === Item::MUSHROOM_STEW){
			$player->getInventory()->removeItem($item);
			$player->setHealth($player->getHealth() + 5);
			$player->setFood(20);
			$player->sendTip($this->getLanguage()->get("player.healed"));
			return;
		}elseif($player->getLevel()->getTile($block) instanceof Sign) {
			$tile = $player->getLevel()->getTile($block);
			$text = $tile->getText();
			if ($text[0] == $this->prefix) {
				if($text[2] == "§aJoin"){
					$this->ArenaJoin($player);
					return;
				}
				$player->sendMessage($this->getLanguage()->get("player.join.error"));
				return;
			}
		}
	}
	
	public function onSignCreate(SignChangeEvent $event){
		$player = $event->getPlayer();
		if($event->getLine(0) == "SoupFFA"){
			if($player->isOp()){
				$event->setLine(0, $this->prefix);
				$event->setLine(2, "§aJoin");
				$event->setLine(3, "§f0 §7/ §c2");
				$player->sendMessage($this->prefix. $this->getLanguage()->get("settings.sign.set"));
				return;
			}
			$event->setCancelled(true);
			$player->sendMessage($this->prefix. $this->getLanguage()->get("player.noperm"));
			return;
		}
	}
	
	public function onDamage(EntityDamageEvent $event) {
		$entity = $event->getEntity();
		$cause = $event->getCause();
		
		if ($cause == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
			if ($event instanceof EntityDamageByEntityEvent) {
				$killer = $event->getDamager();
				$welt = $killer->getLevel()->getFolderName();
				$arenaname = $this->getConfig()->get("arena");
				if($arenaname == $welt){
					if ($killer instanceof Player) {
					$message = $killer->getName();
					$x = $entity->getX();
					$y = $entity->getY();
					$z = $entity->getZ();
					
					$sx = $entity->getLevel()->getSafeSpawn()->getX();
					$sy = $entity->getLevel()->getSafeSpawn()->getY();
					$sz = $entity->getLevel()->getSafeSpawn()->getZ();
					
					$cp = $this->getConfig()->get("spawnprotection");
					
					if(abs($sx - $x) < $cp && abs($sy - $y) < $cp && abs($sz - $z) < $cp){
						
						$event->setCancelled(true);
						
						$killer->sendMessage($this->prefix . $this->getLanguage()->get("player.spawnprotection"));
						return;
					}elseif ($event->getDamage() >= $entity->getHealth()) {
						$event->setCancelled(true);
						
						$arenalevel = $this->getServer()->getLevelByName($arenaname);
						$arenaspawn = $arenalevel->getSafeSpawn();
						$entity->teleport($arenaspawn, 0, 0);
						
						$this->Title($entity, "§4Death", $killer->getName());
						$this->Title($killer, "§2Kill", $entity->getName());
						
						$this->SoupItems($entity);
						$this->SoupItems($killer);
						
						$deathmsg = $this->getLanguage()->get("player.death");
						$deathmsg = str_replace("{player}", $killer->getName(), $deathmsg);
						
						$entity->sendMessage($this->prefix . $deathmsg);
						
						$killmsg = $this->getLanguage()->get("player.kill");
						$killmsg = str_replace("{player}", $entity->getName(), $killmsg);
						$killer->sendMessage($this->prefix . $killmsg);
						return;
						}
					}
				}
			}
		}
	}
	
	/**
	* @param Player $player
	*/
	
	public function SoupItems(Player $player){
		  
		$inv = $player->getInventory();
		$inv->clearAll();
		$slots = array(1,2,3,4,5,6,7,8);
		foreach($slots as $s){
		    $inv->setItem($s, Item::get(282));
		}
		
		if($player->hasPermission("soupffa.vip")){
			$this->vipPlayer($player);
		}else{
			$this->normalPlayer($player);
		}
		$player->setFood(20);
		$player->setHealth(20);
	}
	
	/**
	* @param Player $player
	*/
	
	public function normalPlayer(Player $player){
		$inv = $player->getInventory();
		
		$sword = $this->getConfig()->get("sword");
		
		$inv->setItem(0, Item::get($sword));
		
		$helmet = $this->getConfig()->get("helmet");
		$chestplate = $this->getConfig()->get("chestplate");
		$leggings = $this->getConfig()->get("leggings");
		$boots = $this->getConfig()->get("boots");
		
		$inv->setHelmet(Item::get($helmet));
		$inv->setChestplate(Item::get($chestplate));
		$inv->setLeggings(Item::get($leggings));
		$inv->setBoots(Item::get($boots));
		$inv->sendArmorContents($player);
	}
	
	/**
	* @param Player $player
	*/
	
	public function vipPlayer(Player $player){
		$inv = $player->getInventory();
		
		$sword = $this->getConfig()->get("vipsword");
		
		$inv->setItem(0, Item::get($sword));
		
		$helmet = $this->getConfig()->get("viphelmet");
		$chestplate = $this->getConfig()->get("vipchestplate");
		$leggings = $this->getConfig()->get("vipleggings");
		$boots = $this->getConfig()->get("vipboots");
		
		$inv->setHelmet(Item::get($helmet));
		$inv->setChestplate(Item::get($chestplate));
		$inv->setLeggings(Item::get($leggings));
		$inv->setBoots(Item::get($boots));
		$inv->sendArmorContents($player);
	}
	
	/**
	* @param Player $player
	* @param $line1
	* @param $line2
	*/
	
	public function Title(Player $player, string $line1, string $line2){
		if($this->getServer()->getName() == "PocketMine-MP"){
			$player->addTitle($line1, $line2);
			return;
		}else{
			$player->sendTitle($line1, $line2);
			return;
		}
	}
	
	/**
	* @param Player $player
	*/
	
	public function ArenaJoin(Player $player){
		$arenaname = $this->getConfig()->get("arena");
		
		if(!$this->getServer()->isLevelLoaded($arenaname)){
			$this->getServer()->loadLevel($arenaname);
		}
		
		$arenalevel = $this->getServer()->getLevelByName($arenaname);
		$arenaspawn = $arenalevel->getSafeSpawn();
		$arenalevel->loadChunk($arenaspawn->getX(), $arenaspawn->getZ());
		$player->teleport($arenaspawn, 0, 0);
		$this->SoupItems($player);
		$player->sendMessage( $this->prefix .$this->getLanguage()->get("player.join"));
		$this->updateSign($arenaname);
	}
	
	/**
	* @param Player $player
	*/
	
	public function ArenaLeave(Player $player){
		$default = $this->getServer()->getDefaultLevel();
		$spawn = $default->getSafeSpawn();
		$player->teleport($spawn, 0, 0);
		$player->setFood(20);
		$player->setHealth(20);
		$inv = $player->getInventory();
		$inv->clearAll();
		$player->sendMessage($this->prefix.$this->getLanguage()->get("player.quit"));
		$this->updateSign($this->getConfig()->get("arena"));
	}
	
	#Sign
	
	/*
	* @param $arena
	*/
	
	private function updateSign(string $arena){
		$lobby = $this->getServer()->getDefaultLevel();
		if($this->getServer()->isLevelLoaded($lobby->getFolderName())){
			foreach($lobby->getTiles() as $tile){
				if($tile instanceof Sign){
				$signt = $tile->getText();
				if($signt[0] == $this->prefix){
					if($signt[1] == $arena){
						$status = $this->config->getNested($arena . ".Status");
						$arenalevel = $this->getServer()->getLevelByName($arena);
						$playercount = count($arenalevel->getPlayers());
						if($status == "Ingame"){
							$tile->setText($signt[0], $signt[1], "§cIngame", "§f".$playercount." §7/ §c2");
						}else{
							$tile->setText($signt[0], $signt[1], "§aJoin", "§f".$playercount." §7/ §c2");
						}
					}
				}
				}
			}
		}
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool {
		if($command->getName() == "soupffa"){
			if($sender instanceof Player){
				$player = $sender;
				if(!empty($args[0])){
					if($args[0] == "join"){
					$world = $player->getLevel()->getFolderName();
					$arenaname = $this->getConfig()->get("arena");
						
						if($arenaname == $world){
							$player->sendMessage($this->prefix. $this->getLanguage()->get("player.join.already"));
							return false;
						}else{
							$this->ArenaJoin($player);
							return true;
						}
					
					}elseif($args[0] == "leave" or $args[0] == "quit"){
						$world = $player->getLevel()->getFolderName();
						$arenaname = $this->getConfig()->get("arena");
						
						if($arenaname == $world){
							$this->ArenaLeave($player);
							return true;
						}else{
							$player->sendMessage($this->prefix. $this->getLanguage()->get("player.quit.noarena"));
							return false;
						}
					}
					
				}
				$player->sendMessage($this->prefix. " Syntax: /soupffa <join/quit>!");
				return false;
			}
			$sender->sendMessage($this->prefix. " §7by §6McpeBooster§7!");
			return false;
		}
	}
	
}
