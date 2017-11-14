<?php 

namespace Zero\WorldTpUI\Command;

use pocketmine\Player;

use pocketmine\utils\TextFormat as T;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\command\defaults\VanillaCommand;

class wtpuiCommand extends VanillaCommand {
    
  private $plugin;

  public function __construct(\Zero\WorldTpUI\Main $plugin){
    $this->plugin = $plugin;
    parent::__construct('wtpui', 'allows admins to tp to any world', '/wtpui');
    $this->setPermission('plugins.command');
  }

  public function execute(CommandSender $sender, $alias, array $args){
  if($sender instanceof Player){
  if($sender->isOp() === true){
    $ui = $this->plugin->ui['world-tp'];
    $ui->data = ['type' => 'custom_form', 'title' => 'WorldTpUI '. $this->plugin->version, 
    'content' => [
      ['type' => 'input', 'text' => 'Type a world name', 'placeholder' => 'WorldName', 'default' => null],
      ["type" => "label", "text" => "Worlds Loaded:\n". T::AQUA . $this->getLevels()]
    ]];
    $ui->send($sender);
    return true;
  } else {
    $sender->sendMessage(T::RED."You must be Op to run this Command!");
    return false;
   }
  } else {
    $sender->sendMessage(T::RED."Command must be run in-game!");
    return false;     
   }
  }

  public function getLevels(){
    $levels = $this->plugin->getServer()->getLevels();
  foreach($levels as $level){
    $lvl[$level->getName()] = $level;
  }
    return implode(", ", array_keys($lvl));
    unset($lvl);
  }
}
