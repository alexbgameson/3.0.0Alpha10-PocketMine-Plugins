<?php
namespace rankup\permission;

use pocketmine\Player;
use rankup\RankUp;

abstract class BasePermissionManager{
    private $plugin;
    public function __construct(RankUp $main){
        $this->plugin = $main;
    }
    public function getPlugin(){
        return $this->plugin;
    }
    public abstract function addToGroup(Player $player, $group);
    public abstract function getGroup(Player $player);
    public abstract function getPlayersInGroup($name);
    public abstract function getAPI();
    public abstract function isReady();
    public abstract function getName();
    public function checkReady(){
        if(!$this->isReady()){
            $this->getPlugin()->reportPermissionLinkError();
            return false;
        }
        return true;
    }
}