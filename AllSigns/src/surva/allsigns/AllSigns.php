<?php
/**
 * Created by PhpStorm.
 * User: surva
 * Date: 14.05.16
 * Time: 12:01
 */

namespace surva\allsigns;

use pocketmine\utils\Config;
use surva\allsigns\tasks\SignUpdate;
use pocketmine\plugin\PluginBase;

class AllSigns extends PluginBase {
    /* @var Config */
    private $messages;

    public function onEnable() {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->messages = new Config($this->getFile() . "resources/languages/" . $this->getConfig()->get("language") . ".yml");

        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SignUpdate($this), 60);
    }

    /**
     * Get a translated message
     *
     * @param string $key
     * @param array $replaces
     * @return string
     */
    public function getMessage(string $key, array $replaces = array()): string {
        if($rawMessage = $this->getMessages()->getNested($key)) {
            if(is_array($replaces)) {
                foreach($replaces as $replace => $value) {
                    $rawMessage = str_replace("{" . $replace . "}", $value, $rawMessage);
                }
            }

            return $rawMessage;
        }

        return $key;
    }

    /**
     * @return Config
     */
    public function getMessages(): Config {
        return $this->messages;
    }
}