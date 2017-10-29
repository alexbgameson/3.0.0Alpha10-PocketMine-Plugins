<?php
namespace SalmonDE\StatsPE\Events;

use SalmonDE\StatsPE\Providers\Entry;

class StatsPE_Event extends \pocketmine\event\plugin\PluginEvent
{

    public static $handlerList = null;

    public function __construct(\pocketmine\plugin\Plugin $plugin){
        parent::__construct($plugin);
    }
}
