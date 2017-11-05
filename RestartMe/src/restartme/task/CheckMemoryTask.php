<?php

namespace restartme\task;

use pocketmine\scheduler\PluginTask;
use restartme\utils\MemoryChecker;
use restartme\RestartMe;

class CheckMemoryTask extends PluginTask{
    /** @var RestartMe */
    private $plugin;
    /**
     * @param RestartMe $plugin
     */
    public function __construct(RestartMe $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }
    /** 
     * @return RestartMe 
     */
    public function getPlugin(){
        return $this->plugin;
    }
    /**
     * @param int $currentTick
     */
    public function onRun($currentTick){
        if(!$this->getPlugin()->isTimerPaused()){
            if(MemoryChecker::isOverloaded($this->getPlugin()->getMemoryLimit())){
                $this->getPlugin()->initiateRestart(RestartMe::OVERLOADED);
            }
        }
    }
}