<?php

namespace Zero\WorldTpUI\UI;

use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

use pocketmine\Player;

use pocketmine\utils\TextFormat as T;

class ListenerUI implements Listener {

  private $plugin;
    
  public function __construct(\Zero\WorldTpUI\Main $plugin){
    $this->plugin = $plugin;
  }
    
  public function getPlugin(){
    return $this->plugin;
  }
    
  public function onPacketReceived(\pocketmine\event\server\DataPacketReceiveEvent $e){
    $player = $e->getPlayer();
    $pk = $e->getPacket();
  if($pk instanceof ModalFormResponsePacket){
    $id = $pk->formId;
    $data = json_decode($pk->formData, true);
    //var_dump($data);//debuggging.
    $form = $this->plugin->ui['world-tp'];
  if($id === $form->getId()){
  if($data[0] != '' or $data[0] != null){
  if($this->getPlugin()->getServer()->isLevelLoaded($data[0])){
  if($player->getLevel()->getName() != $data[0]){
    $player->teleport(\pocketmine\Server::getInstance()->getLevelByName($data[0])->getSafeSpawn());
    $player->sendMessage(T::AQUA .'You have teleported to '. $data[0]);
  } else {
    $player->sendMessage(T::RED .'You are already in that world');
  }
  } else {
    $player->sendMessage(T::RED .'It seems that level is not loaded or does not exist');
  }
  } else {
    $player->sendMessage(T::RED .'Please type a world in the input area.');
     }
    }
   }
  }
}