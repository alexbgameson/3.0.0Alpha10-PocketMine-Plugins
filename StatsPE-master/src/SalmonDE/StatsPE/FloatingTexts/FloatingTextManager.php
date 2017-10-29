<?php
namespace SalmonDE\StatsPE\FloatingTexts;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\StatsPE\Base;
use SalmonDE\StatsPE\FloatingTexts\Events\FloatingTextEvent;

class FloatingTextManager
{
    private static $instance = null;

    private $floatingTextConfig;
    private $floatingTexts = [];

    public function __construct(){
        self::$instance = $this;
        $this->floatingTextConfig = new Config(Base::getInstance()->getDataFolder().'floatingtexts.yml', Config::YAML);

        foreach($this->floatingTextConfig->getAll(true) as $key){
            $data = $this->floatingTextConfig->get($key);
            $this->floatingTexts[$data['Position']['Level']][$key] = new FloatingText($key, $data['Position']['X'], $data['Position']['Y'], $data['Position']['Z'], $data['Position']['Level'], $data['Text']);
        }

        Base::getInstance()->getServer()->getPluginManager()->registerEvents(new EventListener(), Base::getInstance());
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function getFloatingText(string $name){
        if(($data = $this->floatingTextConfig->get($name)) !== false){
            return $this->floatingTexts[$data['Position']['Level']][$name];
        }
    }

    public function getAllFloatingTexts() : array{
        return $this->floatingTexts;
    }

    public function addFloatingText(string $name, int $x, int $y, int $z, \pocketmine\level\Level $level){
        if($this->getFloatingText($name) instanceof FloatingText){
            return false;
        }

        $x = round($x);
        $y = round($y);
        $z = round($z);

        foreach(Base::getInstance()->getDataProvider()->getEntries() as $entry){
            $text[$entry->getName()] = TF::AQUA.$entry->getName().': '.TF::GOLD.'{value}';
        }
        $text['Username'] = Base::getInstance()->getMessage('general.header');

        $event = new FloatingTextEvent(Base::getInstance(), new FloatingText($name, $x, $y, $z, $level->getFolderName(), $text), FloatingTextEvent::ADD);
        Base::getInstance()->getServer()->getPluginManager()->callEvent($event);

        if(!$event->isCancelled()){
            $this->floatingTexts[$level->getFolderName()][$name] = $event->getFloatingText();
            foreach($level->getPlayers() as $player){
                $this->floatingTexts[$level->getFolderName()][$name]->sendTextToPlayer($player);
            }

            $data = [
                'Position' => [
                    'X' => $x,
                    'Y' => $y,
                    'Z' => $z,
                    'Level' => $level->getFolderName()
                ],
                'Text' => $text
            ];

            $this->floatingTextConfig->__set($name, $data);
            $this->floatingTextConfig->save(true);
            return true;
        }
        return false;
    }

    public function removeFloatingText(string $name){
        if(!($floatingText = $this->getFloatingText($name)) instanceof FloatingText){
            return false;
        }

        $event = new FloatingTextEvent(Base::getInstance(), $floatingText, FloatingTextEvent::REMOVE);
        Base::getInstance()->getServer()->getPluginManager()->callEvent($event);

        if(!$event->isCancelled()){
            foreach(Base::getInstance()->getServer()->getLevelByName($floatingText->getLevelName())->getPlayers() as $player){
                $this->floatingTexts[$player->getLevel()->getFolderName()][$name]->removeTextForPlayer($player);
            }
            unset($this->floatingTexts[$player->getLevel()->getFolderName()][$name]);

            $this->floatingTextConfig->__unset($name);
            $this->floatingTextConfig->save(true);
            return true;
        }
        return false;
    }
}
