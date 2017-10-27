<?php
namespace rankup\permission;
use pocketmine\Player;

class RankUpDoesGroups extends BasePermissionManager{
    public function addToGroup(Player $player, $group){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->setPlayerGroup($player, $group);
    }

    public function getGroup(Player $player){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->getPlayerGroup($player);
    }

    public function getPlayersInGroup($name){
        if(!$this->checkReady()) return false;
        //TODO limit to players
        return $this->getAPI()->getGroup($name)->getMembers();
    }

    public function getAPI(){
        return $this->getPlugin()->getRankUpDoesGroups();
    }

    public function isReady(){
        return $this->getPlugin()->isDoesGroupsLoaded();
    }

    public function getName(){
        return "RankUpDoesGroups by Falk";
    }
}