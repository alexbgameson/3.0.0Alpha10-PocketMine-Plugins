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

use pocketmine\utils\Config;
use ru\universalcrew\formshop\Home;

class Provider
{
    /**
     * @var Config
     */
    public $shops;

    /**
     * @var Config
     */
    private $messages;

    /**
     * @var Home
     */
    private $home;

    /**
     * Provider constructor.
     * @param Home $home
     */
    function __construct(Home $home)
    {
        $this->home = $home;
        if (!is_file($this->getHome()->getDataFolder() . 'shops.yml') && !is_file($this->getHome()->getDataFolder() . 'messages.yml')) {
            @mkdir($this->getHome()->getDataFolder());
            $this->getHome()->saveResource('shops.yml');
            $this->getHome()->saveResource('messages.yml');
        }
        $this->shops = new Config($this->getHome()->getDataFolder() . 'shops.yml', Config::YAML, []);
        $this->shops->reload();
        $this->messages = new Config($this->getHome()->getDataFolder() . 'messages.yml', Config::YAML, []);
        $this->messages->reload();
    }

    /**
     * @return array
     */
    function getMessages() : array
    {
        return $this->messages->getAll();
    }

    /**
     * @return Config
     */
    function getShops() : Config
    {
        return $this->shops;
    }

    /**
     * @return array
     */
    function getShopsArray() : array
    {
        return $this->shops->getAll();
    }

    /**
     * @return array
     */
    function getShopsCategories() : array
    {
        $all = $this->getShops()->getAll();
        $categories = [];
        foreach ($all as $category => $items) $categories[$category] = $items["name"];
        return $categories;
    }


    /**
     * @param string $category
     * @return bool
     */
    function isCategotyItems(string $category) : bool
    {
        return isset($this->getShops()->getAll()[$category]["items"]);
    }

    /**
     * @param string $category
     * @return array
     */
    function getCategotyItems(string $category) : array
    {
        if ($this->isCategotyItems($category)) return $this->getShops()->getAll()[$category]["items"];
        else return [];
    }

    /**
     * @param string $category
     * @param int $index
     * @return string
     */
    function getStringItem(string $category, int $index) : string
    {
        return $this->getCategotyItems($category)[$index];
    }

    /**
     * @return Home
     */
    function getHome() : Home
    {
        return $this->home;
    }
}