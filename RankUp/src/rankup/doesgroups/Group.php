<?php
namespace rankup\doesgroups;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class Group{
    /** @var \rankup\doesgroups\RankUpDoesGroups  */
    private $main;
    /** @var \pocketmine\permission\Permission  */
    private $perm;
    /** @var Permission[]  */
    private $permsToSet;
    private $entrance;
    private $exit;
    private $name;
    private $members;
    public function __construct(RankUpDoesGroups $main, $name, $perms, $entrance, $exit, $members){
        $this->main = $main;
        $this->name = $name;
        $this->permsToSet = $perms;
        $this->entrance = $entrance;
        $this->exit = $exit;
        $this->members = $members;
    }
    public function getMembers(){
        return $this->members;
    }
    public function addMember(Player $player){
        $this->members[] = $player->getName();
        $this->getMain()->saveMembers();
        $attachment = $player->addAttachment($this->getMain()->getServer()->getPluginManager()->getPlugin("RankUp"));
        foreach($this->permsToSet as $permToSet){
            $attachment->setPermission($permToSet, true);
        }
        $player->removeAttachment($attachment);
        foreach($this->entrance as $cmd){
            $this->getMain()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{name}", $player->getName(), $cmd));
        }
    }
    public function removeMember(Player $player){
        if(in_array($player->getName(), $this->members)){
            unset($this->members[array_search($player->getName(), $this->members)]);
            $this->getMain()->saveMembers();
            $attachment = $player->addAttachment($this->getMain()->getServer()->getPluginManager()->getPlugin("RankUp"));
            foreach($this->permsToSet as $permToSet){
                $attachment->unsetPermission($permToSet);
            }
            $player->removeAttachment($attachment);
            foreach($this->exit as $cmd){
                $this->getMain()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{name}", $player->getName(), $cmd));
            }
            return true;
        }
        else{
            return false;
        }
    }
    public function isMember(Player $player){
        return in_array($player->getName(), $this->members);
    }
    /**
     * @return mixed
     */
    public function getEntrance(){
        return $this->entrance;
    }

    /**
     * @return mixed
     */
    public function getExit(){
        return $this->exit;
    }

    /**
     * @return \rankup\doesgroups\RankUpDoesGroups
     */
    public function getMain(){
        return $this->main;
    }

    /**
     * @return \pocketmine\permission\Permission
     */
    public function getPerm(){
        return $this->perm;
    }

    /**
     * @return \pocketmine\permission\Permission[]
     */
    public function getPermsToSet(){
        return $this->permsToSet;
    }

}