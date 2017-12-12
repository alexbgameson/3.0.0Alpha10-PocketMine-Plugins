<?php
/**
 * Created by PhpStorm.
 * User: surva
 * Date: 14.05.16
 * Time: 12:01
 */

namespace surva\allsigns;

use surva\allsigns\tasks\SignUpdate;
use pocketmine\plugin\PluginBase;

class AllSigns extends PluginBase {
    public function onEnable() {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SignUpdate($this), 60);
    }
}