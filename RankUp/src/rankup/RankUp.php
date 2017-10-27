<?php
namespace rankup;

use pocketmine\utils\Config;
use rankup\command\RankUpCommand;
use rankup\doesgroups\RankUpDoesGroups;
use rankup\economy\BaseEconomy;
use rankup\economy\EconomyLoader;
use pocketmine\plugin\PluginBase;
use rankup\permission\PermissionLoader;
use rankup\permission\BasePermissionManager;
use rankup\rank\RankStore;

class RankUp extends PluginBase{
    /** @var  LanguageConfig */
    private $languageConfig;
    /** @var  RankUpDoesGroups */
    private $rankUpDoesGroups = false;
    /** @var  PermissionLoader */
    private $permissionLoader;
    /** @var  BasePermissionManager */
    private $permManager;
    /** @var  EconomyLoader */
    private $economyLoader;
    /** @var  BaseEconomy */
    private $economy = false;
    /** @var  RankStore */
    private $rankStore;
    /** @var  RankUpCommand */
    private $rankupCommand;
    public function onEnable(){
        $this->saveDefaultConfig();
        $this->languageConfig = new LanguageConfig($this->getConfig());
        $this->loadRankUpDoesGroups();

        $this->permissionLoader = new PermissionLoader($this);
        $this->permissionLoader->load();

        $this->economyLoader = new EconomyLoader($this);
        $this->economyLoader->load();

        $this->rankStore = new RankStore($this);
        $this->rankStore->loadFromConfig();

        $this->rankupCommand = new RankUpCommand($this);
        $this->getServer()->getCommandMap()->register("rankup", $this->rankupCommand);
    }

    /**
     * @return \rankup\LanguageConfig
     */
    public function getLanguageConfig(){
        return $this->languageConfig;
    }

    /**
     * @param \rankup\economy\BaseEconomy $economy
     */
    public function setEconomy(BaseEconomy $economy){
        $this->economy = $economy;
    }

    /**
     * @return \rankup\economy\BaseEconomy
     */
    public function getEconomy(){
        return $this->economy;
    }

    /**
     * @return bool
     */
    public function isLinkedToEconomy(){
        return $this->economy instanceof BaseEconomy;
    }

    /**
     * @return \rankup\economy\EconomyLoader
     */
    public function getEconomyLoader(){
        return $this->economyLoader;
    }

    public function reportEconomyLinkError(){
        $this->getLogger()->critical("The link to " . $this->economy->getName() . " has been lost. Rankup functionality is no longer available.");
        $this->economy = false;
    }

    /**
     * @return \rankup\rank\RankStore
     */
    public function getRankStore(){
        return $this->rankStore;
    }

    /**
     * @param \rankup\permission\BasePermissionManager $permManager
     */
    public function setPermManager(BasePermissionManager $permManager){
        $this->permManager = $permManager;
    }

    /**
     * @return \rankup\permission\BasePermissionManager
     */
    public function getPermManager(){
        return $this->permManager;
    }

    /**
     * @return \rankup\permission\PermissionLoader
     */
    public function getPermissionLoader(){
        return $this->permissionLoader;
    }

    public function reportPermissionLinkError(){
        $this->getLogger()->critical("The link to " . $this->permissionLoader->getName() . " has been lost.");
        $this->economy = false;
    }

    public function loadRankUpDoesGroups(){
        if($this->getConfig()->get('unleash-the-rankupdoesgroups') !== false){
            $this->saveResource("groups.yml");
            $this->rankUpDoesGroups = new RankUpDoesGroups(new Config($this->getDataFolder() . "/groups.yml", Config::YAML), $this->getServer()->getPluginManager()->getPermission("rankup.groups"), $this->getServer());
            $this->getLogger()->info("Loaded DoesGroups.");
        }
    }

    /**
     * @return \rankup\doesgroups\RankUpDoesGroups
     */
    public function getRankUpDoesGroups(){
        return $this->rankUpDoesGroups;
    }

    public function isDoesGroupsLoaded(){
        return $this->rankUpDoesGroups instanceof RankUpDoesGroups;
    }
}