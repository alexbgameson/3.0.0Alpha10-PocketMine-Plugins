<?php

namespace restartme;

use pocketmine\plugin\PluginBase;
use restartme\command\RestartMeCommand;
use restartme\task\AutoBroadcastTask;
use restartme\task\CheckMemoryTask;
use restartme\task\RestartServerTask;

class RestartMe extends PluginBase{
    const NORMAL = 0;
    const OVERLOADED = 1;
    /** @var int */
    private $timer = 0;
    /** @var bool */
    private $paused = false;
    public function onEnable(){
        $this->saveDefaultConfig();
        $this->saveResource("values.txt");
        $this->getServer()->getCommandMap()->register("restartme", new RestartMeCommand($this));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new AutoBroadcastTask($this), ($this->getConfig()->get("broadcastInterval") * 20));
        if($this->getConfig()->get("restartOnOverload") === true){
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckMemoryTask($this), 6000);
            $this->getServer()->getLogger()->notice("Memory overload restarts are enabled. If memory usage goes above ".$this->getMemoryLimit().", the server will restart.");
        }
        else{
            $this->getServer()->getLogger()->notice("Memory overload restarts are disabled.");
        }
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new RestartServerTask($this), 20);
    	$this->setTime($this->getConfig()->get("restartInterval") * 60);
    }
    /** 
     * @return int 
     */
    public function getTime(){
    	return $this->timer;
    }
    /**
     * @return string
     */
    public function getFormattedTime(){
        $time = $this->toArray();
        return $time[0]." hr ".$time[1]." min ".$time[2]." sec";
    }
    /**
     * @return array
     */
    public function toArray(){
        return [
            floor($this->getTime() / 3600), //hour
            floor(($this->getTime() / 60) - (floor($this->getTime() / 3600) * 60)), //minute
            floor($this->getTime() % 60) //second
        ];
    }
    /** 
     * @param int $seconds 
     */
    public function setTime($seconds){
    	$this->timer = (int) $seconds;
    }
    /** 
     * @param int $seconds 
     */
    public function addTime($seconds){
    	if(is_numeric($seconds)) $this->timer += (int) $seconds;
    }
    /** 
     * @param int $seconds 
     */
    public function subtractTime($seconds){
    	if(is_numeric($seconds)) $this->timer -= (int) $seconds;
    }
    /** 
     * @param string $message
     * @param string $messageType
     */
    public function broadcastTime($message, $messageType){
        $outMessage = str_replace(
            [
                "{RESTART_FORMAT_TIME}",
                "{RESTART_HOUR}",
                "{RESTART_MINUTE}",
                "{RESTART_SECOND}",
                "{RESTART_TIME}"
            ], 
            [
                $this->getFormattedTime(),
                $this->toArray()[0],
                $this->toArray()[1],
                $this->toArray()[2],
                $this->getTime()
            ], 
            $message
        );
        switch(strtolower($messageType)){
            case "chat":
                $this->getServer()->broadcastMessage($outMessage);
                break;
            case "popup":
                foreach($this->getServer()->getOnlinePlayers() as $player){
                    $player->sendPopup($outMessage);
                }
                break;
            case "tip":
                foreach($this->getServer()->getOnlinePlayers() as $player){
                    $player->sendTip($outMessage);
                }
                break;
        }
    }
    /** 
     * @param int $mode 
     */
    public function initiateRestart($mode){
        switch($mode){
            case self::NORMAL:
                foreach($this->getServer()->getOnlinePlayers() as $player){
                    $player->kick($this->getConfig()->get("quitMessage"), false);
                }
                $this->getServer()->getLogger()->info($this->getConfig()->get("quitMessage"));
                break;
            case self::OVERLOADED:
                foreach($this->getServer()->getOnlinePlayers() as $player){
                    $player->kick($this->getConfig()->get("overloadQuitMessage"), false);
                }
                $this->getServer()->getLogger()->info($this->getConfig()->get("overloadQuitMessage"));
                break;
        }
        $this->getServer()->shutdown();
    }
    /**
     * @return bool
     */
    public function isTimerPaused(){
        return $this->paused === true;
    }
    /**
     * @param bool $value
     */
    public function setPaused($value = true){
        $this->paused = (bool) $value;
    }
    /**
     * @return string
     */
    public function getMemoryLimit(){
        return strtoupper($this->getConfig()->get("memoryLimit"));
    }
}