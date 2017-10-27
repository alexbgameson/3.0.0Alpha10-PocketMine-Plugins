<?php
namespace rankup\economy;

use rankup\RankUp;

class EconomyLoader{
    private $plugin;
    public function __construct(RankUp $plugin){
        $this->plugin = $plugin;
    }
    public function load(){
        if($this->plugin->getConfig()->get('preferred-economy') !== false){
            $name = $this->plugin->getConfig()->get('preferred-economy');
            try{
                $econ = new $name($this->plugin);
                if($econ instanceof BaseEconomy){
                    if($econ->isReady()){
                        $this->plugin->setEconomy($econ);
                        $this->plugin->getLogger()->info("Loaded " . $econ->getName());
                    }
                    else{
                        $this->plugin->getLogger()->critical("The preferred-economy you specified is not loaded.");
                    }
                }
            }
            catch(\ClassNotFoundException $e){
                $this->plugin->getLogger()->critical("The preferred-economy you specified is not supported.");
            }
        }
        else{
            /*
             * Try loading EconomyS
             */
            $econ = new Economy($this->plugin);
            if($econ->isReady()){
                $this->plugin->setEconomy($econ);
                $this->plugin->getLogger()->info("Loaded " . $econ->getName());
                return;
            }
            /*
             * Try loading PocketMoney
             */
            $econ = new PocketMoney($this->plugin);
            if($econ->isReady()){
                $this->plugin->setEconomy($econ);
                $this->plugin->getLogger()->info("Loaded " . $econ->getName());
                return;
            }
            $this->plugin->getLogger()->critical("No economy found, an economy is not required but certain features will be unavailable.");
        }
    }
}