<?php

namespace restartme\task;

use pocketmine\scheduler\PluginTask;
use restartme\RestartMe;

class RestartServerTask extends PluginTask{
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
            $this->getPlugin()->subtractTime(1);
            if($this->getPlugin()->getTime() <= $this->getPlugin()->getConfig()->get("startCountdown")){
                $this->getPlugin()->broadcastTime($this->getPlugin()->getConfig()->get("countdownMessage"), $this->getPlugin()->getConfig()->get("displayType"));
            }
            if($this->getPlugin()->getTime() < 1){
                $this->getPlugin()->initiateRestart(RestartMe::NORMAL);
            }
        }
    }
}