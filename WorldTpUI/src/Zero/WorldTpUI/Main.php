<?php

namespace Zero\WorldTpUI;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as T;

class Main extends PluginBase {

  public $ui = [];
  public $id = [];
  
  public $version = '0.0.4';

  public function onEnable() : void {
  try {
  if($this->isFirstLoad() === true){
    $this->getLogger()->info(T::YELLOW ."\nHello and Welcone to WorldTpUI\nEdit the config in 'plugins/WorldTpUI/config.yml'");
    $this->getServer()->getPluginManager()->disablePlugin($this);
  } else {
    $this->getLogger()->info(T::YELLOW ."is Loading...");
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
  if($this->config->get('version') === $this->version){
    $this->getLogger()->info(T::AQUA ."Plugin Config is update-to-date.");
  if($this->config->get("load_all_worlds") === true){
    $this->loadAllWorlds();
  }
    $this->createWorldUI();
    $this->getServer()->getPluginManager()->registerEvents(new \Zero\WorldTpUI\UI\ListenerUI($this), $this);
    $this->getServer()->getCommandMap()->register('wtpui', new \Zero\WorldTpUI\Command\wtpuiCommand($this));
    $this->getLogger()->info(T::GREEN ."Everything has Loaded!");
  } else {
    $this->getLogger()->info(T::RED ."\nPlease Delete config in 'plugins/WorldTpUI/config.yml'\nthe config needs to be updated");
    $this->getServer()->getPluginManager()->disablePlugin($this);
    }
   }
  } catch(Exception $e){
    $this->getLogger()->info(T::RED ."Failed to load due to $e");
   }
  }

  public function isFirstLoad(){
  if(is_file($this->getDataFolder() ."config.yml")){
    return false;
  } else {
    @mkdir($this->getDataFolder());
    $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    $config->setAll(array("version" => $this->version, "load_all_worlds" => false));
    $config->save();
    return true;
   }
  }

  public function loadAllWorlds(){
    $worlds = $this->getServer()->getDataPath() . "worlds/";
    $allWorlds = array_slice(scandir($worlds), 2);
  foreach($allWorlds as $world){
    $this->getServer()->loadLevel($world);
   }
  }

  public function createWorldUI(){
    $id = $this->getRandId();
    $ui = new \Zero\WorldTpUI\UI\CustomUI($id);
    $this->ui['world-tp'] = $ui;
  }

  public function getRandId(){
    $rand = rand(1, 1000);
  if(in_array($rand, $this->id)){
    return self::getRandId();
  } else {
    $this->id[] = $rand;
    return $rand;
   }
  }

  public function onDisable() : void {
    $this->getLogger()->info(T::RED ."unloading plugin...");
  if(isset($this->config)){
    $this->config->save();
  }
	  $this->getLogger()->info(T::RED ."has Unloaded, Goodbye!");
  }
}