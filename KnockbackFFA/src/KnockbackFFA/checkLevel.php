<?php

namespace KnockbackFFA;

use pocketmine\scheduler\PluginTask;

class checkLevel extends PluginTask {
	
	public function __construct(KnockbackFFA $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}
	
	public function onRun(int $currentTick){
		$this->plugin->checkLevelTask();
	}
}
