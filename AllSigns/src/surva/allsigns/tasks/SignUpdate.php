<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 14.05.16
 * Time: 12:23
 */

namespace surva\allsigns\tasks;

use surva\allsigns\AllSigns;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class SignUpdate extends PluginTask {
    /* @var AllSigns */
    private $allSigns;

    public function __construct(AllSigns $allSigns) {
        $this->allSigns = $allSigns;

        parent::__construct($allSigns);
    }

    public function onRun(int $currentTick) {
        foreach($this->getAllSigns()->getServer()->getLevels() as $level) {
            foreach($level->getTiles() as $tile) {
                if($tile instanceof Sign) {
                    $text = $tile->getText();

                    $worldText = $this->getAllSigns()->getConfig()->getNested("world.text");

                    if($text[0] == $worldText) {
                        if($this->getAllSigns()->getServer()->isLevelGenerated($text[1])) {
                            if($level = $this->getAllSigns()->getServer()->getLevelByName($text[1])) {
                                $tile->setText($worldText, $text[1], $text[2], $this->getAllSigns()->getMessage("players", array("count" => count($level->getPlayers()))));
                            } else {
                                $tile->setText($worldText, $text[1], $text[2], $this->getAllSigns()->getMessage("players", array("count" => 0)));
                            }
                        } else {
                            $tile->setText($text[0], $text[1], $text[2], $this->getAllSigns()->getMessage("error"));
                        }
                    }
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