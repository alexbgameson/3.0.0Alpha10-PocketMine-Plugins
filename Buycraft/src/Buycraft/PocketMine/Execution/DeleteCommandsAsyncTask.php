<?php

namespace Buycraft\PocketMine\Execution;


use Buycraft\PocketMine\PluginApi;
use pocketmine\scheduler\AsyncTask;

class DeleteCommandsAsyncTask extends AsyncTask{
	private $pluginApi;
	private $commands;

	/**
	 * DeleteCommandsTask constructor.
	 * @param PluginApi $pluginApi
	 * @param array     $commands
	 */
	public function __construct(PluginApi $pluginApi, array $commands){
		$this->pluginApi = $pluginApi;
		$this->commands = $commands;
	}

	/**
	 * Actions to execute when run
	 */
	public function onRun(){
		$this->pluginApi->deleteCommands((array) $this->commands);
	}
}