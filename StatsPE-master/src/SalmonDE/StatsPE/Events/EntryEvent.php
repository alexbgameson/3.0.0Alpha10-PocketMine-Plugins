<?php
namespace SalmonDE\StatsPE\Events;

use SalmonDE\StatsPE\Providers\Entry;

class EntryEvent extends StatsPE_Event implements \pocketmine\event\Cancellable
{

    public static $handlerList = null;

    const ADD = 0;
    const REMOVE = 1;

    private $entry;
    private $type;

    public function __construct(\pocketmine\plugin\Plugin $plugin, Entry $entry, int $type){
        parent::__construct($plugin);
        $this->entry = $entry;
        $this->$type = $type;
    }

    public function getEntry() : Entry{
        return $this->entry;
    }

    public function getType() : int{
        return $this->type;
    }
}
