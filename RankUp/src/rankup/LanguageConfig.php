<?php
namespace rankup;

use pocketmine\utils\Config;

class LanguageConfig{
    private $lang;

    public function __construct(Config $config){
        $this->lang = $config->get('lang');
    }

    public function getLangSetting($name){
        return $this->lang[$name];
    }
}