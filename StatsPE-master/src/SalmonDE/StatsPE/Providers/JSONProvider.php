<?php
namespace SalmonDE\StatsPE\Providers;

use pocketmine\utils\Config;
use SalmonDE\StatsPE\Base;
use SalmonDE\StatsPE\Events\EntryEvent;

class JSONProvider implements DataProvider
{
    private $entries = [];
    private $dataConfig = null;

    public function __construct(string $path){
        $this->initialize(['path' => $path]);
    }

    public function initialize(array $data){
        $this->dataConfig = new Config($data['path'], Config::JSON);
    }

    public function getName() : string{
        return 'JSONProvider';
    }

    public function addPlayer(\pocketmine\Player $player){
        foreach($this->getEntries() as $entry){ // Run through all entries and save the default values
            $this->saveData($player->getName(), $entry, $entry->getDefault());
        }
    }

    public function getData(string $player, Entry $entry){
        if($this->entryExists($entry->getName())){
            if(!$entry->shouldSave()){
                return;
            }
            $v = $this->dataConfig->getNested(strtolower($player).'.'.$entry->getName());

            $event = new \SalmonDE\StatsPE\Events\DataReceiveEvent(Base::getInstance(), $v, $player, $entry);
            Base::getInstance()->getServer()->getPluginManager()->callEvent($event);
            return $event->getData();
        }
    }

    public function getDataWhere(Entry $needleEntry, $needle, array $wantedEntries){
        if($this->entryExists($needleEntry->getName()) && $needleEntry->shouldSave()){
            if($wantedEntries === []){
                return [];
            }

            foreach($this->getAllData() as $player => $playerData){
                foreach($wantedEntries as $entry){
                    if(!$entry->shouldSave()){
                        $resultData[$player][$entry->getName()] = null;
                        continue;
                    }

                    $resultData[$player][$entry->getName()] = $playerData[$entry->getName()];
                }
            }
            return $resultData;
        }
    }

    public function getAllData(string $player = null){
        if($player !== null){

            $event = new \SalmonDE\StatsPE\Events\DataReceiveEvent(Base::getInstance(), $this->dataConfig->get(strtolower($player), null));
            Base::getInstance()->getServer()->getPluginManager()->callEvent($event);
            return $event->getData();
        }

        $event = new \SalmonDE\StatsPE\Events\DataReceiveEvent(Base::getInstance(), $this->dataConfig->getAll());
        Base::getInstance()->getServer()->getPluginManager()->callEvent($event);
        return $event->getData();
    }

    public function saveData(string $player, Entry $entry, $value){
        if($this->entryExists($entry->getName()) && $entry->shouldSave()){
            if($entry->isValidType($value)){

                $event = new \SalmonDE\StatsPE\Events\DataSaveEvent(Base::getInstance(), $value, $player, $entry);
                Base::getInstance()->getServer()->getPluginManager()->callEvent($event);
                if(!$event->isCancelled()){
                    $this->dataConfig->setNested(strtolower($player).'.'.$entry->getName(), $value);
                }
            }else{
                Base::getInstance()->getLogger()->error($msg = 'Unexpected datatype "'.gettype($value).'" given for entry "'.$entry->getName().'" in "'.self::class.'" by "'.__FUNCTION__.'"!');
            }
        }
    }

    public function incrementValue(string $player, Entry $entry, int $int = 1){
        if($this->entryExists($entry->getName()) && $entry->shouldSave() && $entry->getExpectedType() === Entry::INT){
            $this->saveData($player, $entry, $this->getData($player, $entry) + $int);
        }
    }

    public function addEntry(Entry $entry){
        if(!$this->entryExists($entry->getName()) && $entry->isValid()){
            $event = new EntryEvent(Base::getInstance(), $entry, EntryEvent::ADD);
            Base::getInstance()->getServer()->getPluginManager()->callEvent($event);

            if(!$event->isCancelled()){
                $this->entries[$entry->getName()] = $entry;
                return true;
            }
        }
        return false;
    }

    public function removeEntry(Entry $entry){
        if($this->entryExists($entry->getName())){
            $event = new EntryEvent(Base::getInstance(), $entry, EntryEvent::REMOVE);
            Base::getInstance()->getServer()->getPluginManager()->callEvent($event);

            if(!$event->isCancelled()){
                unset($this->entries[$entry->getName()]);
            }
        }
    }

    public function getEntries() : array{
        return $this->entries;
    }

    public function getEntry(string $entry){
        if(isset($this->entries[$entry])){
            return $this->entries[$entry];
        }
    }

    public function entryExists(string $entry) : bool{
        return isset($this->entries[$entry]);
    }

    public function countDataRecords() : int{
        return count($this->getAllData());
    }

    public function saveAll(){
        $this->dataConfig->save();
    }
}
