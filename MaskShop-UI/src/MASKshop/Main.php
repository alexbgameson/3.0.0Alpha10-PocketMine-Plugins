<?php

namespace MASKshop;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\entity\Effect;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

use onebone\economyapi\EconomyAPI;

use jojoe77777\FormAPI;

class Main extends PluginBase implements Listener {
	
	
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		
		@mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
		
        $this->getLogger()->info("§aEnabled");
    }
	
    public function onDisable() {
        $this->getLogger()->info("§cDisabled");
    }
   
     
     
    public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args) : bool {
		
		switch($cmd->getName()){
		
			case "mask":			    
				if($sender instanceof Player) {					 					    
						$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
						if($api === null || $api->isDisabled()){
						
						}
						$form = $api->createSimpleForm(function (Player $sender, array $data){
						$result = $data[0];
						
						if($result === null){
						
						}
							switch($result){
								
								case 0:
									
									break;
								case 1:
									
							      	 $money = $this->eco->myMoney($sender);
					                $zombie = $this->getConfig()->get("money.zombie");
									if($money >= $zombie){
										
                                       $this->eco->reduceMoney($sender, $zombie);
                                       $sender->getInventory()->addItem(Item::get(397, 2, 1));
									   $sender->sendMessage($this->getConfig()->get("msg.shop.zombie"));
                                      return true;
                                    }else{
                                       $sender->sendMessage($this->getConfig()->get("msg.no-money"));
                                    }
									
									break;
								case 2:
								
								    $money = $this->eco->myMoney($sender);
								    $issuchitel = $this->getConfig()->get("money.issuchitel");
									if($money >= $issuchitel){
										
                                       $this->eco->reduceMoney($sender, $issuchitel);
                                       $sender->getInventory()->addItem(Item::get(397, 1, 1));
									   $sender->sendMessage($this->getConfig()->get("msg.shop.issuchitel"));
                                      return true;
                                    }else{
                                       $sender->sendMessage($this->getConfig()->get("msg.no-money"));
                                    }
									
									break;
								case 3:
								    
									$money = $this->eco->myMoney($sender);
									$dragon = $this->getConfig()->get("money.dragon");
									if($money >= $dragon){
										
                                       $this->eco->reduceMoney($sender, $dragon);
                                       $sender->getInventory()->addItem(Item::get(397, 5, 1));
									   $sender->sendMessage($this->getConfig()->get("msg.shop.dragon"));
                                      return true;
                                    }else{
                                       $sender->sendMessage($this->getConfig()->get("msg.no-money"));
                                    }
									
									break;
								case 4:
								    
									$money = $this->eco->myMoney($sender);
									$creeper = $this->getConfig()->get("money.creeper");
									if($money >= $creeper){
										
                                       $this->eco->reduceMoney($sender, $creeper);
                                       $sender->getInventory()->addItem(Item::get(397, 4, 1));
									   $sender->sendMessage($this->getConfig()->get("msg.shop.creeper"));
                                      return true;
                                    }else{
                                       $sender->sendMessage($this->getConfig()->get("msg.no-money"));
                                    }
									
									break;
								
							}
					
						});
						
				    $money = $this->eco->myMoney($sender);
					$zombie = $this->getConfig()->get("money.zombie");
					$issuchitel = $this->getConfig()->get("money.issuchitel");
					$dragon = $this->getConfig()->get("money.dragon");
					$creeper = $this->getConfig()->get("money.creeper");
					
					$form->setTitle("§bMask Shop");
					$form->setContent("§fYou have §6{$money} §fMoney.");
					
					$form->addButton("§eHeads List");
				    $form->addButton("§8Zombie = §6$".$zombie, 1, "http://i.piccy.info/i9/02004901adf40dfb882978b8599fc678/1509381626/2951/1192083/zombie.png");
					$form->addButton("§8Wither Skeleton = §6$".$issuchitel, 1, "http://i.piccy.info/i9/4a2098870dd5afe9036014ea5a55ad22/1509381594/2386/1192083/issychitel.png");
					$form->addButton("§8Dragon = §6$".$dragon, 1, "http://i.piccy.info/i9/95cb7e55dc8a66b9db5e8d23e9d32a73/1509381549/2657/1192083/dragon.png");
					$form->addButton("§8Creeper = §6$".$creeper, 1, "http://i.piccy.info/i9/3799f426f583bc18192ec868752a07a3/1509381437/5635/1192083/creeper.png");
					
					$form->sendToPlayer($sender);
				}
				else{
					$sender->sendMessage("§l§cYou are not ingame");
					return true;
				}
			break;
		}
		return true;
    }
	
	
	
}
