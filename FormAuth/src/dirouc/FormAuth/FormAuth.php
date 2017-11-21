<?php

namespace dirouc\FormAuth;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Player;
use pocketmine\OfflinePlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\PluginTask;
use pocketmine\command\{Command, CommandSender};

class FormAuth extends PluginBase implements Listener {

    private $auth_players = [];

    private $auth_attempts = [];

    private $formAPI;

    private $messages;

    public function onEnable() : void {
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "players/");
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->formAPI = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        if(!$this->formAPI) {
            $this->getLogger()->info(TextFormat::YELLOW . "FormAPI plugin not found! Disabling the plugin...");
            $this->getLogger()->info(TextFormat::DARK_RED . "Plugin disabled.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        } elseif($this->formAPI) {
            $this->getLogger()->info(TextFormat::YELLOW . "FormAPI plugin found! Enabling the plug-in...");
            $this->getLogger()->info(TextFormat::DARK_GREEN . "Plugin enabled.");
        }

        $this->messages = new Messages($this);
    }

    public function getMessage($node, ...$vars) {
        return $this->messages->getMessage($node, ...$vars);
    }

    public function getFAVersion() {
        return $this->getDescription()->getVersion();
    }

    public function translateColors($message) : string {
        $message = str_replace("&0", TextFormat::BLACK, $message);
        $message = str_replace("&1", TextFormat::DARK_BLUE, $message);
        $message = str_replace("&2", TextFormat::DARK_GREEN, $message);
        $message = str_replace("&3", TextFormat::DARK_AQUA, $message);
        $message = str_replace("&4", TextFormat::DARK_RED, $message);
        $message = str_replace("&5", TextFormat::DARK_PURPLE, $message);
        $message = str_replace("&6", TextFormat::GOLD, $message);
        $message = str_replace("&7", TextFormat::GRAY, $message);
        $message = str_replace("&8", TextFormat::DARK_GRAY, $message);
        $message = str_replace("&9", TextFormat::BLUE, $message);
        $message = str_replace("&a", TextFormat::GREEN, $message);
        $message = str_replace("&b", TextFormat::AQUA, $message);
        $message = str_replace("&c", TextFormat::RED, $message);
        $message = str_replace("&d", TextFormat::LIGHT_PURPLE, $message);
        $message = str_replace("&e", TextFormat::YELLOW, $message);
        $message = str_replace("&f", TextFormat::WHITE, $message);

        $message = str_replace("&k", TextFormat::OBFUSCATED, $message);
        $message = str_replace("&l", TextFormat::BOLD, $message);
        $message = str_replace("&m", TextFormat::STRIKETHROUGH, $message);
        $message = str_replace("&n", TextFormat::UNDERLINE, $message);
        $message = str_replace("&o", TextFormat::ITALIC, $message);
        $message = str_replace("&r", TextFormat::RESET, $message);

        return $message;
    }

    private function grep(string $path, string $str) : int {
        $count = 0;
        foreach(glob($path . "*.json") as $filename) {
            foreach(file($filename) as $fli=>$fl) {
                if(strpos($fl, $str) !== false) {
                    $count += 1;
                }
            }
        }
        return $count;
    }

    public function getPlayerData(string $player) {
        if($this->isPlayerRegistered($player)) {
            $cfg = new Config($this->getDataFolder() . "players/" . strtolower($player . ".json"), Config::JSON);
            return $cfg->getAll();
        } else {
            return $this->isPlayerRegistered($player);
        }
    }

    public function isPlayerRegistered(string $player) {
        $status = file_exists($this->getDataFolder() . "players/" . strtolower($player . ".json"));
        return $status;
    }

    public function isPlayerAuthenticated(Player $player) : bool {
        return isset($this->auth_players[strtolower($player->getName())]);
    }

    public function registerPlayer(Player $player, string $password) {
        if($this->isPlayerRegistered($player->getName())) {
            $player->sendMessage($this->translateColors($this->getMessage("already-registered")));
            return $this->reCreateForm($player);
        } else {
            if(mb_strlen($password) <= $this->getConfig()->get("minPasswordLength")) {
                $player->sendMessage($this->translateColors($this->getMessage("short-password")));
                return $this->reCreateForm($player);
            } elseif(mb_strlen($password) >= $this->getConfig()->get("maxPasswordLength")) {
                $player->sendMessage($this->translateColors($this->getMessage("long-password")));
                return $this->reCreateForm($player);
            } else {
                if($this->grep($this->getDataFolder() . "players/", $player->getAddress()) + 1 <= 1) {
                    $data = new Config($this->getDataFolder() . "players/" . strtolower($player->getName() . ".json"), Config::JSON);
                    $data->set("password", password_hash($password, PASSWORD_DEFAULT));
                    $data->set("firstip", $player->getAddress());
                    $data->set("lastip", $player->getAddress());
                    $data->set("firstlogin", time());
                    $data->set("lastlogin", time());
                    $data->set("firstclientid", $player->getClientId());
                    $data->set("lastclientid", $player->getClientId());
                    $data->save();
                    $this->auth_players[strtolower($player->getName())] = "";
                    return $player->sendMessage($this->translateColors($this->getMessage("success-register")));
                } else {
                    return $player->sendMessage($this->translateColors($this->getMessage("ip-register")));
                }
            }
        }
    }

    public function deauthenticatePlayer(Player $player) {
        if($this->isPlayerAuthenticated($player)) {
            unset($this->auth_players[strtolower($player->getName())]);
        } else {
            return false;
        }
    }

    public function authenticatePlayer(Player $player, string $password) {
        if($this->isPlayerRegistered($player->getName())) {
            if(!$this->isPlayerAuthenticated($player)) {
                $data = new Config($this->getDataFolder() . "players/" . strtolower($player->getName() . ".json"), Config::JSON);
                if(password_verify($password, $data->get("password"))) {
                    $data->set("lastip", $player->getAddress());
                    $data->set("lastlogin", time());
                    $data->save();
                    $this->auth_players[strtolower($player->getName())] = "";
                    return $player->sendMessage($this->translateColors($this->getMessage("auth-success")));
                } else {
                    if(isset($this->auth_attempts[strtolower($player->getName())])) {
                        $this->auth_attempts[strtolower($player->getName())]++;
                    } else {
                        $this->auth_attempts[strtolower($player->getName())] = 1;
                    }
                    if($this->auth_attempts[strtolower($player->getName())] >= 3) {
                        $player->close("", $this->translateColors($this->getMessage("many-password-attempts")));
                        unset($this->auth_attempts[strtolower($player->getName())]);
                    }
                    $player->sendMessage($this->translateColors($this->getMessage("wrong-password")));
                    return $this->reCreateForm($player);
                }
            } else {
                $player->sendMessage($this->translateColors($this->getMessage("already-auth")));
            }
        } else {
            return $this->isPlayerRegistered($player->getName());
        }
    }

    public function changePlayerPassword($player, string $new_password) {
        if($player instanceof Player || $player instanceof OfflinePlayer) {
            if($this->isPlayerRegistered($player->getName())) {
                if(mb_strlen($new_password) <= $this->getConfig()->get("minPasswordLength")) {
                    $player->sendMessage($this->translateColors($this->getMessage("short-password")));
                } elseif(mb_strlen($new_password) >= $this->getConfig()->get("maxPasswordLength")) {
                    $player->sendMessage($this->translateColors($this->getMessage("long-password")));
                } else {
                    $data = new Config($this->getDataFolder() . "players/" . strtolower($player->getName() . ".json"), Config::JSON);
                    $data->set("password", password_hash($new_password, PASSWORD_DEFAULT));
                    $data->save();
                    return $player->sendMessage($this->translateColors($this->getMessage("changepassword-success")));
                }
            }else{
                return $this->isPlayerRegistered($player->getName());
            }
        }else{
            return $player->sendMessage($this->translateColors($this->getMessage("no-already-register")));
        }
    }

    public function reCreateForm($player) {
        $this->startCountDownTimer($player, $this->getConfig()->get("create-form-sec"));
    }

    public function startCountDownTimer($player, $secsTotal) {
        $this->seconds = time();
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new countdownTimer($this, $player, $secsTotal), 20);
    }

    public function createForm(int $id, $player) {
        switch($id) {
            case 0:
                $form = $this->formAPI->createCustomForm(function (Player $player, array $data) {
                    $result = $data[0];
                    if ($result === null) {
                        $this->reCreateForm($player);
                        return true;
                    }
                    switch ($result) {
                        case 0:
                            if(!empty($data[0]) && !empty($data[1])) {
                                if($data[0] == $data[1]) {
                                    $this->registerPlayer($player, $data[0]);
                                } else {
									$this->reCreateForm($player);
								}
                            } else {
                                $this->reCreateForm($player);
                            }
                            return true;
                    }
                });
                $form->setTitle($this->translateColors($this->getMessage("form.register-title")));
                $form->addInput($this->translateColors($this->getMessage("form.input-password", $this->getConfig()->get("minPasswordLength"), $this->getConfig()->get("maxPasswordLength"))));
                $form->addInput($this->translateColors($this->getMessage("form.confirm-input-password")));
                $form->sendToPlayer($player);
                break;
            case 1:
                $form = $this->formAPI->createCustomForm(function (Player $player, array $data) {
                    $result = $data[0];
                    if ($result === null) {
                        $this->reCreateForm($player);
                        return true;
                    }
                    switch ($result) {
                        case 0:
                            if(!empty($data[0])) {
                                $this->authenticatePlayer($player, $data[0]);
                            } else {
                                $this->reCreateForm($player);
                            }
                            return true;
                    }
                });
                $form->setTitle($this->translateColors($this->getMessage("form.auth-title")));
                $form->addInput($this->translateColors($this->getMessage("form.input-passw")));
                $form->sendToPlayer($player);
                break;
            case 2:
                $form = $this->formAPI->createCustomForm(function (Player $player, array $data) {
                    $result = $data[0];
                    if ($result === null) {
                        return true;
                    }
                    switch ($result) {
                        case 0:
                            if(!empty($data[0]) && !empty($data[1])) {
                                if($data[0] == $data[1]) {
                                    $this->changePlayerPassword($player, $data[0]);
                                } else {
                                   return $player->sendMessage($this->translateColors($this->getMessage("password-password")));
                                }
                            }
                            return true;
                    }
                });
                $form->setTitle($this->translateColors($this->getMessage("form.changepassword-title")));
                $form->addInput($this->translateColors($this->getMessage("form.input-new_password")));
                $form->addInput($this->translateColors($this->getMessage("form.input-confirm_new_password")));
                $form->sendToPlayer($player);
                break;
        }
    }

    public function onPreLogin(PlayerPreLoginEvent $event) {
        $player = $event->getPlayer();
        if($this->getConfig()->get("force-single-auth") == true) {
            foreach($this->getServer()->getOnlinePlayers() as $pl) {
                if(strtolower($pl->getName()) == strtolower($player->getName())) {
                    $player->close("", $this->translateColors($this->getMessage("already-play")));
                    $event->setCancelled(true);
                }
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->reCreateForm($player);
    }

    public function onPlayerMove(PlayerMoveEvent $event) {
        if(!$this->isPlayerAuthenticated($event->getPlayer())) {
            if($this->getConfig()->get("allow-move") == false) {
                $event->setCancelled(true);
            } 
        }
    }

    public function onPlayerChat(PlayerChatEvent $event) {
        if($this->getConfig()->get("block-chat") == true) {
            if(!$this->isPlayerAuthenticated($event->getPlayer())) {
                $event->setCancelled(true);
            }
            $recipients = $event->getRecipients();
            foreach($recipients as $key => $recipient) {
                if($recipient instanceof Player) {
                    if(!$this->isPlayerAuthenticated($recipient)) {
                        unset($recipients[$key]);
                    }
                }
            }
            $event->setRecipients($recipients);
        }
    }

    public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if($this->getConfig()->get("block-commands") == true) {
            if(!$this->isPlayerAuthenticated($event->getPlayer())) {
                $command = strtolower($event->getMessage());
                if ($command{0} == "/") {
                    $event->setCancelled(true);
                }
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        if(!$this->isPlayerAuthenticated($event->getPlayer())) {
            $event->setCancelled(true);
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        if(!$this->isPlayerAuthenticated($event->getPlayer())) {
            $event->setCancelled(true);
        }
    }

    public function onEntityDamage(EntityDamageEvent $event) {
        $player = $event->getEntity();
        if($player instanceof Player) {
            if(!$this->isPlayerAuthenticated($player)) {
                $event->setCancelled(true);
            }
        }
        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if($damager instanceof Player) {
                if(!$this->isPlayerAuthenticated($damager)) {
                    $event->setCancelled(true);
                }
            }
        }
    }

    public function onDropItem(PlayerDropItemEvent $event) {
        if($this->getConfig()->get("block-all-events")) {
            if(!$this->isPlayerAuthenticated($event->getPlayer())) {
                $event->setCancelled(true);
            }
        }    
    }

    public function onItemConsume(PlayerItemConsumeEvent $event) {
        if($this->getConfig()->get("block-all-events")) {
            if(!$this->isPlayerAuthenticated($event->getPlayer())) {
                $event->setCancelled(true);
            }
        }
    }

    public function onCraftItem(CraftItemEvent $event) {
        if($this->getConfig()->get("block-all-events")) {
            if(!$this->isPlayerAuthenticated($event->getPlayer())) {
                $event->setCancelled(true);
            }
        }
    }

    public function onAwardAchievement(PlayerAchievementAwardedEvent $event) {
        if($this->getConfig()->get("block-all-events")) {
            if(!$this->isPlayerAuthenticated($event->getPlayer())) {
                $event->setCancelled(true);
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if($command->getName() == "changepassword") {
            $this->createForm(2, $sender);
        }
        return true;
    }

}

class countdownTimer extends PluginTask {

    public function __construct(PluginBase $owner, Player $player, int $secsTotal) {
        parent::__construct($owner);
        $this->player = $player;
        $this->secsTotal = $secsTotal;
    }

    public function onRun($currentTick){
        $this->endingtime = $this->getOwner()->seconds + $this->secsTotal;
        $this->secondsLeft = $this->endingtime - time();
        if($this->getOwner()->getConfig()->get("debug-message"))
            $this->player->sendTip($this->getOwner()->translateColors("&e" . $this->secondsLeft . "&r"));
        if($this->secondsLeft <= 0){
            if($this->getOwner()->isPlayerAuthenticated($this->player)) {
                $playerdata = $this->getOwner()->getPlayerData($this->player->getName());        
                if($this->getOwner()->getConfig()->get("IPLogin") == true) {
                   if($playerdata["lastip"] == $this->player->getAddress()) {
                        //
                    } else {
                        $this->getOwner()->deauthenticatePlayer($this->player);
                    }
                } else {
                    $this->getOwner()->deauthenticatePlayer($this->player);
                }     
            }
            if(!$this->getOwner()->isPlayerRegistered($this->player->getName())) {
                $this->getOwner()->createForm(0, $this->player);
            } else {
                if(!$this->getOwner()->isPlayerAuthenticated($this->player)) {
                    $this->getOwner()->createForm(1, $this->player);
                }
            }
            $this->getOwner()->getServer()->getScheduler()->cancelTasks($this->getOwner());
        }
    }
}