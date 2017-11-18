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

class Forms
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
     */
    function mainShopForm(Player $player)
    {
        $money = $this->getHome()->getEconomy()->myMoney($player);
        $form = $this->getHome()->getForm()->createSimpleForm(function (Player $player, array $data) {
            if (!($data[0] === null )) {
                $category = $data[0];
                $category = array_keys($this->getHome()->getProvider()->getShopsArray())[$category];
                $this->itemsForm($category, $player);
            }
        });
        $form->setTitle($this->getHome()->getProvider()->getMessages()['mainform']['title']);
        $content = str_replace("%money%", $money, $this->getHome()->getProvider()->getMessages()['mainform']['content']);
        $form->setContent($content);
        $categories = $this->getHome()->getProvider()->getShopsCategories();
        foreach ($categories as $name) {
            $string = str_replace("%category_name%", $name, $this->getHome()->getProvider()->getMessages()['mainform']['category_button']);
            $form->addButton($string);
        }
        $form->sendToPlayer($player);
    }

    /**
     * @param string $category
     * @param Player $player
     */
    private function itemsForm(string $category, Player $player)
    {
        if ($this->getHome()->getProvider()->getCategotyItems($category)) {
            if ($player instanceof Player) {
                $money = $this->getHome()->getEconomy()->myMoney($player);
                $form = $this->getHome()->getForm()->createSimpleForm(function (Player $player, array $data) use ($category) {
                    if (!($data[0] === null )) {
                        if (count($this->getHome()->getProvider()->getCategotyItems($category)) <= $data[0]) $this->mainShopForm($player);
                        else $this->selectCountItem($category, $data[0], $player);
                    }
                });
                $form->setTitle($this->getHome()->getProvider()->getMessages()['itemsform']['title']);
                $content = str_replace("%money%", $money, $this->getHome()->getProvider()->getMessages()['itemsform']['content']);
                $form->setContent($content);
                $items = $this->getHome()->getProvider()->getCategotyItems($category);
                foreach ($items as $item) {
                    list($id, $damage, $price) = explode(":", $item);
                    $item = new Item($id, $damage);
                    $string = $this->getHome()->getProvider()->getMessages()['itemsform']['item_button'];
                    $string = str_replace("%item_name%", $item->getName(), $string);
                    $string = str_replace("%id%", $id, $string);
                    $string = str_replace("%damage%", $damage, $string);
                    $string = str_replace("%price%", $price, $string);
                    $form->addButton($string);
                }
                $form->addButton($this->getHome()->getProvider()->getMessages()['mainformreturn']);
                $form->sendToPlayer($player);
            }
        } else {
            $player->sendTip($this->getHome()->getProvider()->getMessages()['itemsform']['no_content']);
            $this->mainShopForm($player);
        }
    }

    /**
     * @param string $category
     * @param int $index
     * @param Player $player
     */
    private function selectCountItem(string $category, int $index, Player $player)
    {
        if ($player instanceof Player) {
            $money = $this->getHome()->getEconomy()->myMoney($player);
            $string_item = $this->getHome()->getProvider()->getStringItem($category, $index);
            list($id, $damage, $price) = explode(":", $string_item);
            $item = new Item($id, $damage);
            $string = $this->getHome()->getProvider()->getMessages()['selectcountform']['title'];
            $string = str_replace("%item_name%", $item->getName(), $string);
            $string = str_replace("%id%", $id, $string);
            $string = str_replace("%damage%", $damage, $string);
            $string = str_replace("%price%", $price, $string);
            $string = str_replace("%money%", $money, $string);
            $form = $this->getHome()->getForm()->createCustomForm(function (Player $player, array $data) use ($string_item) {
                if (!($data[0] === null )) {
                    $this->buyForm($player, $data, $string_item);
                }
            });
            $form->setTitle($string);
            $form->addSlider($this->getHome()->getProvider()->getMessages()['selectcountform']['slider_name'], 1, 64, 1, 1);
            $form->sendToPlayer($player);
        }
    }

    /**
     * @param Player $player
     * @param array $data
     * @param string $string_item
     */
    private function buyForm(Player $player, array $data, string $string_item)
    {
        if ($player instanceof Player) {
            $money = $this->getHome()->getEconomy()->myMoney($player);
            list($id, $damage, $price) = explode(":", $string_item);
            $count = $data[0];
            $fullprice = $count * $price;
            $item = new Item($id, $damage);
            $string = $this->getHome()->getProvider()->getMessages()['buyform']['content'];
            $string = str_replace("%item_name%", $item->getName(), $string);
            $string = str_replace("%id%", $id, $string);
            $string = str_replace("%damage%", $damage, $string);
            $string = str_replace("%price%", $price, $string);
            $string = str_replace("%money%", $money, $string);
            $string = str_replace("%count%", $count, $string);
            $string = str_replace("%fullprice%", $fullprice, $string);
            $form = $this->getHome()->getForm()->createSimpleForm(function (Player $player, array $data) use ($money, $fullprice, $item, $count) {
                if (!($data[0] === null )) {
                    if ($fullprice > $money) $this->mainShopForm($player);
                    else {
                        switch ($data[0]) {
                            case 0:
                                $this->getHome()->getPay()->pay($player, $fullprice, $item, $count);
                                break;
                            case 1:
                                $this->mainShopForm($player);
                                break;
                        }
                    }
                }

            });
            $form->setTitle($this->getHome()->getProvider()->getMessages()['buyform']['title']);
            if ($fullprice > $money) {
                $text = $this->getHome()->getProvider()->getMessages()['buyform']['no_money'];
                $text = str_replace("%fullprice%", $fullprice, $text);
                $form->setContent($text);
                $form->addButton($this->getHome()->getProvider()->getMessages()['mainformreturn']);
            } else {
                $form->setContent($string);
                $form->addButton($this->getHome()->getProvider()->getMessages()['buyform']['buy_button']);
                $form->addButton($this->getHome()->getProvider()->getMessages()['mainformreturn']);
            }
            $form->sendToPlayer($player);
        }
    }

    /**
     * @return Home
     */
    function getHome(): Home
    {
        return $this->home;
    }

}