<?php
/**
 * Created by PhpStorm.
 * User: surva
 * Date: 14.05.16
 * Time: 12:01
 */

namespace surva\allsigns;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;

class EventListener implements Listener {
    /* @var AllSigns */
    private $allSigns;

    public function __construct(AllSigns $allSigns) {
        $this->allSigns = $allSigns;
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $action = $event->getAction();
        $block = $event->getBlock();

        if(
            (
                $block->getId() == Block::SIGN_POST OR
                $block->getId() == Block::WALL_SIGN
            ) AND
            $action == PlayerInteractEvent::RIGHT_CLICK_BLOCK
        ) {
            $tile = $block->getLevel()->getTile($block);

            if($tile instanceof Sign) {
                $text = $tile->getText();

                switch($text[0]) {
                    case $this->getAllSigns()->getConfig()->get("world"):
                        if($this->getAllSigns()->getServer()->isLevelGenerated($text[1])) {
                            if($level = $this->getAllSigns()->getServer()->getLevelByName($text[1])) {
                                $tile->setText($this->getAllSigns()->getConfig()->get("worldtext"), $text[1], $text[2], count($level->getPlayers()) . " " . $this->getAllSigns()->getConfig()->get("players"));
                            } else {
                                $tile->setText($this->getAllSigns()->getConfig()->get("worldtext"), $text[1], $text[2], "0 " . $this->getAllSigns()->getConfig()->get("players"));
                            }
                        } else {
                            $block->getLevel()->setBlock($block, Block::get(Block::AIR));

                            $player->sendMessage($this->getAllSigns()->getConfig()->get("noworld"));
                        }
                        break;
                    case $this->getAllSigns()->getConfig()->get("command"):
                        $tile->setText($this->getAllSigns()->getConfig()->get("commandtext"), $text[1], $text[2], $text[3]);
                        break;
                    case $this->getAllSigns()->getConfig()->get("worldtext"):
                        $this->getAllSigns()->getServer()->loadLevel($text[1]);

                        if($level = $this->getAllSigns()->getServer()->getLevelByName($text[1])) {
                            $player->teleport($level->getSafeSpawn());
                        } else {
                            $player->sendMessage($this->getAllSigns()->getConfig()->get("noworld"));
                        }
                        break;
                    case $this->getAllSigns()->getConfig()->get("commandtext"):
                        $this->getAllSigns()->getServer()->dispatchCommand($player, $text[2] . $text[3]);
                        break;
                }
            }
        }
    }

    /**
     * @return AllSigns
     */
    public function getAllSigns(): AllSigns {
        return $this->allSigns;
    }
}