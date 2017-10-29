<?php
namespace SalmonDE\StatsPE;

use pocketmine\utils\Config;
use SalmonDE\StatsPE\Providers\Entry;

class Base extends \pocketmine\plugin\PluginBase
{

    /** @var Base */
    private static $instance = null;
    /** @var Providers\JSONProvider|Providers\MySQLProvider */
    private $provider = null;
    /** @var string[] */
    private $messages = [];

    /** @var EventListener */
    private $listener = null; // Needed because of the OnlineTime hack
    /** @var FloatingTexts\FloatingTextManager */
    private $floatingTextManager = null;

    /**
     * @return Base
     */
    public static function getInstance() : Base{
        return self::$instance;
    }

    /**
     * @return void
     */
    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
        $this->saveResource('messages.yml');
        $this->initializeProvider();
        if($this->isEnabled()){

            if(!file_exists($this->getDataFolder().'messages.yml')){
                if($this->getResource($lang = ($this->getConfig()->get('Language').'.yml')) === null){
                    $lang = 'English.yml';
                }
                $this->saveResource($lang);
                rename($this->getDataFolder().$lang, $this->getDataFolder().'messages.yml');
            }

            $msgConfig = new Config($this->getDataFolder().'messages.yml', Config::YAML);
            $this->messages = $msgConfig->getAll();

            $this->registerDefaultEntries();
            if($this->provider instanceof Providers\MySQLProvider){
                $this->provider->prepareTable();
            }
            $this->registerCommands();

            if(($i = $this->getConfig()->getNested('JSON.saveInterval')) >= 1){
                $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new Tasks\SaveTask($this), $i *= 1200, $i);
            }else{
                $this->getLogger()->warning('The save interval is lower than 1 min! Please make sure to always properly shutdown the server in order to prevent data loss!');
            }
            $this->floatingTextManager = $this->floatingTextManager instanceof FloatingTexts\FloatingTextManager ? $this->floatingTextManager : new FloatingTexts\FloatingTextManager();
            $this->getServer()->getPluginManager()->registerEvents($this->listener = new EventListener(), $this);
        }
    }

    /**
     * @return void
     */
    public function onDisable(){
        if(!$this->getServer()->isRunning()){
            foreach($this->getServer()->getOnlinePlayers() as $player){
                $this->listener->onQuit(new \pocketmine\event\player\PlayerQuitEvent($player, '')); // Hacky, but prevents not saving online time of players on shutdown
            }
        }
        if(isset($this->provider)){
            $this->provider->saveAll();
        }
        $this->listener = null;
    }

    /**
     * @return void
     */
    private function initializeProvider(){
        if($this->provider instanceof Providers\DataProvider){
            return;
        }

        switch($p = $this->getConfig()->get('Provider')){
            case 'json':
                $this->getLogger()->info('Selecting JSON data provider ...');
                $this->provider = new Providers\JSONProvider($this->getDataFolder().'players.json');
                break;

            case 'mysql':
                $this->getLogger()->info('Selecting MySQL data provider ...');
                $this->provider = new Providers\MySQLProvider(($c = $this->getConfig())->getNested('MySQL.host'), $c->getNested('MySQL.username'), $c->getNested('MySQL.password'), $c->getNested('MySQL.database'), $c->getNested('MySQL.cacheLimit'));
                break;

            default:
                $this->getLogger()->warning('Unknown provider: "'.$p.'", selecting JSON data provider...');
                $this->provider = new Providers\JSONProvider($this->getDataFolder().'players.json');
        }
    }

    /**
     * @return void
     */
    private function registerDefaultEntries(){
        $this->provider->addEntry(new Entry('Username', 'undefined', Entry::STRING, true));
        foreach($this->getConfig()->get('Stats') as $statistic => $enabled){
            if($enabled){
                $unsigned = false;

                switch($statistic){
                    case 'Online':
                        $default = false;
                        $expectedType = Entry::BOOL;
                        $save = true;
                        break;

                    case 'ClientID':
                        $default = 'undefined';
                        $expectedType = Entry::STRING;
                        $save = true;
                        break;

                    case 'LastIP':
                        $default = 'undefined';
                        $expectedType = Entry::STRING;
                        $save = true;
                        break;

                    case 'UUID':
                        $default = 'undefined';
                        $expectedType = Entry::STRING;
                        $save = true;
                        break;

                    case 'XBoxAuthenticated':
                        $default = false;
                        $expectedType = Entry::BOOL;
                        $save = false; //Not yet
                        break;

                    case 'OnlineTime':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'FirstJoin':
                        $default = 0.0;
                        $expectedType = Entry::FLOAT;
                        $save = true;
                        break;

                    case 'LastJoin':
                        $default = 0.0;
                        $expectedType = Entry::FLOAT;
                        $save = true;
                        break;

                    case 'K/D':
                        $default = 0.0;
                        $expectedType = Entry::FLOAT;
                        $save = false;
                        break;

                    case 'JoinCount':
                        $default = 1;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'KillCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'DeathCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'BlockBreakCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'BlockPlaceCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'ChatCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'ItemConsumeCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'ItemCraftCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                        break;

                    case 'ItemDropCount':
                        $default = 0;
                        $expectedType = Entry::INT;
                        $save = true;
                        $unsigned = true;
                }
                $this->provider->addEntry(new Entry($statistic, $default, $expectedType, $save, $unsigned));
            }
        }
        if($this->getDataProvider()->entryExists('K/D')){
            if(!$this->getDataProvider()->entryExists('KillCount') || !$this->getDataProvider()->entryExists('DeathCount')){
                $this->getLogger()->warning('Disabled K/D entry due to error prevention! Did you enable KillCount and DeathCount in the config?');
                $this->getDataProvider()->removeEntry($this->getDataProvider()->getEntry('K/D'));
            }
        }
    }

    /**
     * @return void
     */
    private function registerCommands(){
        $this->getServer()->getCommandMap()->register('statspe', new Commands\StatsCmd($this));
        $this->getServer()->getCommandMap()->register('statspe', new Commands\StatsPECmd($this));
    }

    /*private function unregisterCommands(){
        $cmdMap = $this->getServer()->getCommandMap();
        $cmdMap->getCommand('statspe:stats')->unregister($cmdMap);
        $cmdMap->getCommand('statspe:statspe')->unregister($cmdMap);
    }*/

    /**
     * @return Providers\DataProvider
     */
    public function getDataProvider() : Providers\DataProvider{
        return $this->provider;
    }

    /**
     * @param Providers\DataProvider $provider
     *
     * @return void
     */
    public function setDataProvider(Providers\DataProvider $provider){
        $this->provider = $provider;
    }

    /**
     * @return FloatingTexts\FloatingTextManager
     */
    public function getFloatingTextManager() : FloatingTexts\FloatingTextManager{
        return $this->floatingTextManager;
    }


    /**
     * @param string $k
     *
     * @return string
     */
    public function getMessage(string $k){
        $keys = explode('.', $k);
        $message = $this->messages['lines'];
        foreach($keys as $k){
            $message = $message[$k];
        }
        return $message;
    }
}
