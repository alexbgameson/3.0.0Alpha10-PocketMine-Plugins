<?php
namespace SalmonDE\StatsPE\FloatingTexts\Events;

use SalmonDE\StatsPE\FloatingTexts\FloatingText;

class FloatingTextEvent extends \SalmonDE\StatsPE\Events\StatsPE_Event implements \pocketmine\event\Cancellable
{

    public static $handlerList = null;

    const ADD = 0;
    const REMOVE = 1;

    private $floatingText;
    private $type;

    public function __construct(\pocketmine\plugin\Plugin $plugin, FloatingText $floatingText, int $type){
        parent::__construct($plugin);
        $this->floatingText = $floatingText;
        $this->type = $type;
    }


    public function getFloatingText() : FloatingText{
        return $this->floatingText;
    }

    public function getType() : int{
        return $this->type;
    }
}
