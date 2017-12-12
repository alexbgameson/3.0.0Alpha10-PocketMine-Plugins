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

                $worldIdentifier = $this->getAllSigns()->getConfig()->getNested("world.identifier");
                $worldText = $this->getAllSigns()->getConfig()->getNested("world.text");

                $commandIdentifier = $this->getAllSigns()->getConfig()->getNested("command.identifier");
                $commandText = $this->getAllSigns()->getConfig()->getNested("command.text");

                switch($text[0]) {
                    case $worldIdentifier:
                        if($this->getAllSigns()->getServer()->isLevelGenerated($text[1])) {
                            if($level = $this->getAllSigns()->getServer()->getLevelByName($text[1])) {
                                $tile->setText($worldText, $text[1], $text[2], $this->getAllSigns()->getMessage("players", array("count" => count($level->getPlayers()))));
                            } else {
                                $tile->setText($worldText, $text[1], $text[2], $this->getAllSigns()->getMessage("players", array("count" => 0)));
                            }
                        } else {
                            $block->getLevel()->setBlock($block, Block::get(Block::AIR));

                            $player->sendMessage($this->getAllSigns()->getMessage("noworld"));
                        }
                        break;
                    case $commandIdentifier:
                        $tile->setText($commandText, $text[1], $text[2], $text[3]);
                        break;
                    case $worldText:
                        $this->getAllSigns()->getServer()->loadLevel($text[1]);

                        if($level = $this->getAllSigns()->getServer()->getLevelByName($text[1])) {
                            $player->teleport($level->getSafeSpawn());
                        } else {
                            $player->sendMessage($this->getAllSigns()->getMessage("noworld"));
                        }
                        break;
                    case $commandText:
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