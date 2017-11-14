<?php

namespace Zero\WorldTpUI\UI;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

abstract class UI {

  public $id;
  public $data = [];
  public $player;

  public function __construct($id) {
    $this->id = $id;
  }

  public function getId(){
    return $this->id;
  }

  public function send($player){
    $pk = new ModalFormRequestPacket();
    $pk->formId = $this->id;
    $pk->formData = json_encode($this->data);
    $player->dataPacket($pk);
  }
}