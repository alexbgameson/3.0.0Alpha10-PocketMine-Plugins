<?php
namespace SalmonDE\StatsPE\FloatingTexts;

use pocketmine\utils\TextFormat as TF;
use SalmonDE\StatsPE\Base;
use SalmonDE\StatsPE\Utils;

class FloatingText extends \pocketmine\level\particle\FloatingTextParticle
{
    private $name;
    private $floatingText = [];
    private $levelName;

    public function __construct(string $name, int $x, int $y, int $z, string $levelName, array $text){
        parent::__construct(new \pocketmine\math\Vector3($x, $y, $z), '');
        $this->name = $name;
        $this->floatingText = $text;
        $this->levelName = $levelName;
    }

    public function getName() : string{
        return $this->name;
    }

    public function getFloatingText() : array{
        return $this->floatingText;
    }

    public function getLevelName() : string{
        return $this->levelName;
    }

    public function sendTextToPlayer(\pocketmine\Player $player){
        $data = Base::getInstance()->getDataProvider()->getAllData($player->getName());
        $text = [];
        foreach(array_keys($this->floatingText) as $key){
            if(Base::getInstance()->getDataProvider()->entryExists($key)){
                switch($key){
                    case 'FirstJoin':
                        $value = date(Base::getInstance()->getConfig()->get('dateFormat'), $player->getFirstPlayed() / 1000);
                        break;

                    case 'LastJoin':
                        $value = date(Base::getInstance()->getConfig()->get('dateFormat'), $player->getLastPlayed() / 1000);
                        break;

                    case 'OnlineTime':
                        $seconds = $data['OnlineTime'];
                        $seconds += ceil(microtime(true) - ($player->getLastPlayed() / 1000));

                        $value = Utils::getPeriodFromSeconds($seconds);
                        break;

                    case 'K/D':
                        $value = Utils::getKD($data['KillCount'], $data['DeathCount']);
                        break;

                    case 'Online':
                        $value = $data['Online'] ? Base::getInstance()->getMessage('commands.stats.true') : Base::getInstance()->getMessage('commands.stats.false');
                        break;

                    default:
                        $value = $data[$key];
                }
                $text[] = str_replace('{value}', $value, $this->floatingText[$key]);
            }
        }
        $this->setTitle(array_shift($text));
        $text = implode(TF::RESET."\n", $text);

        $this->setText($text);
        $player->getLevel()->addParticle($this, [$player]);
        $this->setTitle(' ');
        $this->setText(' ');
    }

    public function removeTextForPlayer(\pocketmine\Player $player){
        $this->setInvisible();
        $player->getLevel()->addParticle($this, [$player]);
        $this->setInvisible(false);
    }
}
