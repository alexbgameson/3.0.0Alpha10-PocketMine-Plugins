<?php

namespace restartme\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use restartme\utils\MemoryChecker;
use restartme\RestartMe;

class RestartMeCommand extends Command{
    /** @var RestartMe */
    private $plugin;
    /**
     * @param RestartMe $plugin
     */
    public function __construct(RestartMe $plugin){
        parent::__construct("restartme");
        $this->setDescription("Shows all RestartMe commands");
        $this->setUsage("/restartme <sub-command> [parameters]");
        $this->setPermission("restartme.command.restartme");
        $this->setAliases(["rm"]);
        $this->plugin = $plugin;
    }
    /** 
     * @return RestartMe 
     */
    public function getPlugin(){
        return $this->plugin;
    }
    /** 
     * @param CommandSender $sender 
     */
    private function sendCommandHelp(CommandSender $sender){
        $commands = [
            "add" => "Adds n seconds to the timer",
            "help" => "Shows all RestartMe commands",
            "memory" => "Shows memory usage information",
            "set" => "Sets the timer to n seconds",
            "start" => "Starts the timer",
            "stop" => "Stops the timer",
            "subtract" => "Subtracts n seconds from the timer",
            "time" => "Gets the remaining time until the server restarts"
        ];
        $sender->sendMessage("RestartMe commands:");
        foreach($commands as $name => $description){
            $sender->sendMessage("/restartme ".$name.": ".$description);
        }
    }
    /**
     * @param CommandSender $sender
     * @param string $label
     * @param string[] $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args){
        if(!$this->testPermission($sender)) return false;
        if(isset($args[0])){
            switch(strtolower($args[0])){
                case "a":
                case "add":
                    if(isset($args[1])){
                        if(is_numeric($args[1])){
                            $time = (int) $args[1];
                            $this->getPlugin()->addTime($time);
                            $sender->sendMessage(TextFormat::GREEN."Added ".$time." to restart timer.");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Time value must be numeric.");
                        } 
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a time value.");
                    }
                    return true;
                case "help":
                    $this->sendCommandHelp($sender);
                    return true;
                case "m":
                case "memory":
                    $memLimit = $this->getPlugin()->getMemoryLimit();
                    $sender->sendMessage("Bytes: ".memory_get_usage(true)."/".MemoryChecker::calculateBytes($memLimit));
                    $sender->sendMessage("Memory-limit: ".$memLimit);
                    $sender->sendMessage("Overloaded: ".(MemoryChecker::isOverloaded($memLimit) ? TextFormat::GREEN."yes" : TextFormat::RED."no"));
                    return true;
                case "set":
                    if(isset($args[1])){
                        if(is_numeric($args[1])){
                            $time = (int) $args[1];
                            $this->getPlugin()->setTime($time);
                            $sender->sendMessage(TextFormat::GREEN."Set restart timer to ".$time.".");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Time value must be numeric.");
                        } 
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a time value.");
                    }
                    return true;
                case "start":
                    if($this->getPlugin()->isTimerPaused()){
                        $this->getPlugin()->setPaused(false);
                        $sender->sendMessage(TextFormat::YELLOW."Timer is no longer paused.");
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Timer is not paused.");
                    }
                    return true;
                case "stop":
                    if($this->getPlugin()->isTimerPaused()){
                        $sender->sendMessage(TextFormat::RED."Timer is already paused.");
                    }
                    else{
                        $this->getPlugin()->setPaused(true);
                        $sender->sendMessage(TextFormat::YELLOW."Timer has been paused.");
                    }
                    return true;
                case "s":
                case "subtract":
                    if(isset($args[1])){
                        if(is_numeric($args[1])){
                            $time = (int) $args[1];
                            $this->getPlugin()->subtractTime($time);
                            $sender->sendMessage(TextFormat::GREEN."Subtracted ".$time." from restart timer.");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED."Time value must be numeric.");
                        } 
                    }
                    else{
                        $sender->sendMessage(TextFormat::RED."Please specify a time value.");
                    }
                    return true;
                case "time":
                    $sender->sendMessage(TextFormat::YELLOW."Time remaining: ".$this->getPlugin()->getFormattedTime());
                    return true;
                default:
                    $sender->sendMessage("Usage: ".$this->getUsage());
                    return false;
            }
        }
        else{
            $this->sendCommandHelp($sender);
            return false;
        }
    }
}