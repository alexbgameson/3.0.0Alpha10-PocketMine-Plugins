<?php
namespace SalmonDE\StatsPE\Events;

class DataSaveEvent extends DataEvent implements \pocketmine\event\Cancellable
{

    public static $handlerList = null;

    public function __construct(\pocketmine\plugin\Plugin $plugin, $data, string $player, \SalmonDE\StatsPE\Providers\Entry $entry){
        parent::__construct($plugin, $data, $player, $entry);
    }
}
