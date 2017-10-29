<?php
namespace SalmonDE\StatsPE\Commands;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\StatsPE\FloatingTexts\FloatingTextManager;


class StatsPECmd extends \pocketmine\command\PluginCommand implements \pocketmine\command\CommandExecutor
{

    public function __construct(\SalmonDE\StatsPE\Base $owner){
        parent::__construct('statspe', $owner);
        $this->setPermission('statspe.cmd.statspe');
        $this->setDescription($owner->getMessage('commands.statspe.description'));
        $this->setUsage($owner->getMessage('commands.statspe.usage'));
        $this->setExecutor($this);
    }

    public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $cmd, string $label, array $args): bool{
        if(isset($args[0])){
            if(strtolower($args[0]) === 'floatingtext' && isset($args[1])){
                if(!$sender->hasPermission('statspe.cmd.statspe.floatingtext')){
                    $sender->sendMessage(new \pocketmine\event\TranslationContainer(TF::RED.'%commands.generic.permission'));
                    return true;
                }

                switch(strtolower($args[1])){
                    case 'add':
                        if(!$sender instanceof Player){
                            $this->getPlugin()->getMessage('commands.statspe.floatingtext.senderNotPlayer');
                        }
                        if(isset($args[2])){
                            if(FloatingTextManager::getInstance()->addFloatingText($args[2], $sender->x, $sender->y, $sender->z, $sender->getLevel())){
                                $sender->sendMessage(str_replace('{name}', $args[2], $this->getPlugin()->getMessage('commands.statspe.floatingtext.addSuccess')));
                            }else{
                                $sender->sendMessage(str_replace('{name}', $args[2], $this->getPlugin()->getMessage('commands.statspe.floatingtext.alreadyExists')));
                            }
                        }else{
                            $sender->sendMessage($this->getPlugin()->getMessage('commands.statspe.floatingtext.missingName'));
                        }
                        break;

                    case 'remove':
                        if(isset($args[2])){
                            if(FloatingTextManager::getInstance()->removeFloatingText($args[2])){
                                $sender->sendMessage(str_replace('{name}', $args[2], $this->getPlugin()->getMessage('commands.statspe.floatingtext.removeSuccess')));
                            }else{
                                $sender->sendMessage($this->getPlugin()->getMessage(str_replace('{name}', $args[2], 'commands.statspe.floatingtext.notFound')));
                            }
                        }else{
                            $sender->sendMessage($this->getPlugin()->getMessage('commands.statspe.floatingtext.missingName'));
                        }
                        break;

                    case 'list':
                        foreach(FloatingTextManager::getInstance()->getAllFloatingTexts() as $levelList){
                            foreach($levelList as $floatingText){
                                $texts[] = $floatingText->getName();
                            }
                        }

                        $sender->sendMessage(str_replace(['{count}', '{names}'], [count($texts), implode(', ', $texts)], $this->getPlugin()->getMessage('commands.statspe.floatingtext.listAll')));
                        break;

                    case 'info':
                        if(isset($args[2])){
                            if(($ft = FloatingTextManager::getInstance()->getFloatingText($args[2])) instanceof \SalmonDE\StatsPE\FloatingTexts\FloatingText){
                                $info = [
                                    '{name}' => $ft->getName(),
                                    '{x}' => $ft->x,
                                    '{y}' => $ft->y,
                                    '{z}' => $ft->z,
                                    '{level}' => $ft->getLevelName(),
                                    '{entries}' => implode(', ', array_keys($ft->getFloatingText()))
                                ];

                                $lines = [
                                    $this->getPlugin()->getMessage('commands.statspe.floatingtext.info.name'),
                                    $this->getPlugin()->getMessage('commands.statspe.floatingtext.info.position'),
                                    $this->getPlugin()->getMessage('commands.statspe.floatingtext.info.entries')
                                ];
                                $lines = implode(TF::RESET."\n", $lines);

                                $sender->sendMessage(str_replace(array_keys($info), array_values($info), $lines));
                            }else{
                                $sender->sendMessage($this->getPlugin()->getMessage(str_replace('{name}', $args[2], 'commands.statspe.floatingtext.notFound')));
                            }
                        }else{
                            $sender->sendMessage($this->getPlugin()->getMessage('commands.statspe.floatingtext.missingName'));
                        }
                        break;
                    default:
                        return false;
                }
            }else{
                return false;
            }
        }else{
            $messages = [
                $this->getPlugin()->getMessage('commands.statspe.header'),
                $this->getPlugin()->getMessage('commands.statspe.author'),
                $this->getPlugin()->getMessage('commands.statspe.api'),
                $this->getPlugin()->getMessage('commands.statspe.provider'),
                $this->getPlugin()->getMessage('commands.statspe.datarecords'),
                $this->getPlugin()->getMessage('commands.statspe.entries')
            ];

            foreach($this->getPlugin()->getDataProvider()->getEntries() as $entry){
                $entries[] = $entry->getName();
            }

            $values = [
                '{full_name}' => $this->getPlugin()->getDescription()->getFullName(),
                '{author}' => implode(', ', $this->getPlugin()->getDescription()->getAuthors()),
                '{apis}' => implode(', ', $this->getPlugin()->getDescription()->getCompatibleApis()),
                '{provider}' => $this->getPlugin()->getDataProvider()->getName(),
                '{records_amount}' => $this->getPlugin()->getDataProvider()->countDataRecords(),
                '{entries}' => implode('; ', $entries)
            ];

            $sender->sendMessage(str_replace(array_keys($values), array_values($values), implode(TF::RESET."\n", $messages)));
        }
        return true;
    }
}
