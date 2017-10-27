<?php
namespace rankup\permission;

use rankup\RankUp;

class PermissionLoader{
    private $plugin;
    private $name;
    public function __construct(RankUp $plugin){
        $this->plugin = $plugin;
    }
    public function load(){
        if($this->plugin->getConfig()->get('preferred-groupmanager') !== false){
            $name = $this->plugin->getConfig()->get('preferred-groupmanager');
            try{
                $permManager = new $name($this->plugin);
                $this->name = $permManager->getName();
                if($permManager instanceof BasePermissionManager){
                    if($permManager->isReady()){
                        $this->plugin->setPermManager($permManager);
                        $this->plugin->getLogger()->info("Loaded " . $permManager->getName());
                    }
                    else{
                        $this->plugin->getLogger()->critical("The preferred-groupmanager you specified is not loaded.");
                    }
                }
            }
            catch(\ClassNotFoundException $e){
                $this->plugin->getLogger()->critical("The preferred-groupmanager you specified is not supported.");
            }
        }
        else{
            //TODO autoload PurePerms and RankUpDoesGroups
        }
    }
    public function getName() : string{
    	return $this->name;
	}
}