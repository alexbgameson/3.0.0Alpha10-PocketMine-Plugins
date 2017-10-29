<?php
namespace SalmonDE\StatsPE\FloatingTexts;

class EventListener implements \pocketmine\event\Listener
{

    /**
    * @priority MONITOR
    */
    public function onJoin(\pocketmine\event\player\PlayerJoinEvent $event){
        $floatingTexts = FloatingTextManager::getInstance()->getAllFloatingTexts();
        if(isset($floatingTexts[$event->getPlayer()->getLevel()->getFolderName()])){
            foreach($floatingTexts[$event->getPlayer()->getLevel()->getFolderName()] as $floatingText){
                $floatingText->sendTextToPlayer($event->getPlayer());
            }
        }
    }


    /**
    * @priority MONITOR
    */
    public function onEntityLevelChange(\pocketmine\event\entity\EntityLevelChangeEvent $event){
         if(!$event->isCancelled()){
             if($event->getEntity() instanceof \pocketmine\Player){
                 $floatingTexts = FloatingTextManager::getInstance()->getAllFloatingTexts();

                 if(isset($floatingTexts[$event->getOrigin()->getFolderName()])){
                     foreach($floatingTexts[$event->getOrigin()->getFolderName()] as $floatingText){
                         $floatingText->removeTextForPlayer($event->getEntity());
                     }
                 }

                 if(isset($floatingTexts[$event->getTarget()->getFolderName()])){
                     foreach($floatingTexts[$event->getTarget()->getFolderName()] as $floatingText){
                         $floatingText->sendTextToPlayer($event->getEntity());
                     }
                 }
             }
         }
    }
}
