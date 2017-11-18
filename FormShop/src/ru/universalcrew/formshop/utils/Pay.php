<?php

namespace ru\universalcrew\formshop\utils;

/**
 *  _    _       _                          _  ____
 * | |  | |_ __ (_)_    _____ _ ______ __ _| |/ ___\_ _______      __
 * | |  | | '_ \| | \  / / _ \ '_/ __// _' | / /   | '_/ _ \ \    / /
 * | |__| | | | | |\ \/ /  __/ | \__ \ (_) | \ \___| ||  __/\ \/\/ /
 *  \____/|_| |_|_| \__/ \___|_| /___/\__,_|_|\____/_| \___/ \_/\_/
 *
 * @author egr7v8
 * @link   https://t.me/egr7v8
 *
 */

use pocketmine\item\Item;
use pocketmine\Player;
use ru\universalcrew\formshop\Home;

class Pay
{
    /**
     * @var Home
     */
    private $home;

    /**
     * Pay constructor.
     * @param Home $home
     */
    function __construct(Home $home)
    {
        $this->home = $home;
    }

    /**
     * @param Player $player
     * @param string $money
     * @return bool
     */
    function checkMoney(Player $player, string $money) : bool
    {
        return $this->getHome()->getEconomy()->myMoney($player) >= $money;
    }

    /**
     * @param Player $player
     * @param int $fullprice
     * @param Item $item
     */
    public function pay(Player $player, int $fullprice, Item $item, int $count)
    {
        if ($player->getInventory()->canAddItem($item)) {
            $this->getHome()->getEconomy()->reduceMoney($player, $fullprice);
            $text = $this->getHome()->getProvider()->getMessages()['pay'];
            $text = str_replace("%item_name%", $item->getName(), $text);
            $text = str_replace("%count%", $count, $text);
            $player->getInventory()->addItem(Item::get($item->getId(), $item->getDamage(), $count));
            $player->sendMessage($text);
        } else $player->sendMessage($this->getHome()->getProvider()->getMessages()['full_inventory']);
    }

    /**
     * @return Home
     */
    function getHome() : Home
    {
        return $this->home;
    }

}