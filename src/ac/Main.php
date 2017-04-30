<?php

    namespace ac;

    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use pocketmine\network\protocol\AdventureSettingsPacket;
    use pocketmine\event\server\DataPacketReceiveEvent;
    use pocketmine\network\protocol\UpdateAttributesPacket;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerQuitEvent;
    use pocketmine\event\player\PlayerKickEvent;
    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\utils\TextFormat;
    use pocketmine\Player;

    class Main extends PluginBase implements Listener {

        public $movePlayers = [];

        public $point = [];

        public function onEnable() {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckTask($this), 20);
        }

        public function onPlayerKick(PlayerKickEvent $event){
            if($event->getReason() === "Sorry, hack mods are not permitted on Steadfast... at all."){
                $event->setCancelled(true);
            }
    	}

        public function onPlayerJoin(PlayerJoinEvent $event){
            $player = $event->getPlayer();
            $this->movePlayers[$player->getName()]["distance"] = 0;
            $this->point[$player->getName()]["distance"] = 0;
    	}

        public function onPlayerQuit(PlayerQuitEvent $event){
            $player = $event->getPlayer();
            unset($this->movePlayers[$player->getName()]);
            unset($this->point[$player->getName()]);
    	}

        public function onPlayerMove(PlayerMoveEvent $event){
            $player = $event->getPlayer();
            $oldPos= $event->getFrom();
		    $newPos = $event->getTo();
            if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight()){
                $this->movePlayers[$player->getName()]["distance"] += sqrt(($newPos->getX() - $oldPos->getX()) ** 2 + ($newPos->getZ() - $oldPos->getZ()) ** 2);
            }
    	}

        public function onRecieve(DataPacketReceiveEvent $event) {
            $player = $event->getPlayer();
            $packet = $event->getPacket();
            if($packet instanceof UpdateAttributesPacket){ 
                $player->kick(TextFormat::RED."HACK UpdateAttributesPacket");
            }
            if($packet instanceof SetPlayerGameTypePacket){ 
                $player->kick(TextFormat::RED."HACK SetPlayerGameTypePacket");
            }
            if($packet instanceof AdventureSettingsPacket){
                if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight()){
                    switch ($packet->flags){
                        case 614:
                        case 102:
                        case 615:
                        case 103:
                            $player->kick(TextFormat::RED."HACK Fly and NoClip");
                            break;
                        default:
                            break;
                    }
                    if((($packet->flags >> 9) & 0x01 === 1) or (($packet->flags >> 7) & 0x01 === 1) or (($packet->flags >> 6) & 0x01 === 1)){
                        $player->kick(TextFormat::RED."HACK Fly and NoClip");
                    }
                }
            }
        }
    }