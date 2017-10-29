<?php
namespace SalmonDE\StatsPE\Events;

class DataReceiveEvent extends DataEvent
{

    public static $handlerList = null;

    public function __construct(\pocketmine\plugin\Plugin $plugin, $data, string $player = null, \SalmonDE\StatsPE\Providers\Entry $entry = null){
        parent::__construct($plugin, $data, $player, $entry);
    }
}
