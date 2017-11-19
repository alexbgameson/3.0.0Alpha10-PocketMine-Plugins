<?php

namespace dirouc\FormAuth;

use pocketmine\utils\Config;

class Messages {

    private $language, $messages, $langList = [];

    public function __construct(FormAuth $plugin) {
        $this->plugin = $plugin;

        $this->registerLanguages();

        $this->loadMessages();
    }

    public function registerLanguages() {
        $result = [];

        foreach($this->plugin->getResources() as $resource) {
            if(mb_strpos($resource, "messages-") !== false) $result[] = substr($resource, -6, -4);
        }

        $this->langList = $result;
    }

    public function getMessage($node, ...$vars) {
        $msg = $this->messages->getNested($node);

        if($msg != null) {
            $number = 0;

            foreach($vars as $v) {
                $msg = str_replace("%var$number%", $v, $msg);

                $number++;
            }

            return $msg;
        }

        return null;
    }

    public function getVersion() {
        $version = $this->messages->get("messages-version");
        return $version;
    }

    public function loadMessages() {
        $defaultLang = $this->plugin->getConfig()->get("default-language");

        foreach($this->langList as $langName) {
            if(strtolower($defaultLang) == $langName) {
                $this->language = $langName;
            }
        }

        if(!isset($this->language)) {
            $this->plugin->getLogger()->warning("Language resource " . $defaultLang . " not found. Using default language resource by " . $this->plugin->getDescription()->getAuthors()[0]);

            $this->language = "en";
        }

        $this->plugin->saveResource("messages-" . $this->language . ".yml");

        $this->messages = new Config($this->plugin->getDataFolder() . "messages-" . $this->language . ".yml", Config::YAML, [
        ]);

        $this->plugin->getLogger()->info("Setting default language to '" . $defaultLang . "'");

        if(version_compare($this->getVersion(), $this->plugin->getFAVersion()) === -1) {
            $this->plugin->saveResource("messages-" . $this->language . ".yml", true);

            $this->messages = new Config($this->plugin->getDataFolder() . "messages-" . $this->language . ".yml", Config::YAML, [
            ]);
        }
    }

    public function reloadMessages() {
        $this->messages->reload();
    }

}