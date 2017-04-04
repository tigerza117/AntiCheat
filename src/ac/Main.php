<?php

    namespace ac;

    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use pocketmine\network\protocol\AdventureSettingsPacket;
    use pocketmine\network\protocol\PlayerActionPacket;
    use pocketmine\event\server\DataPacketReceiveEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\entity\Effect;
    use pocketmine\utils\TextFormat;
    use pocketmine\math\Vector3;

    class Main extends PluginBase implements Listener {

        public $players = [];

        public function onEnable() {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
        }

        public function onPlayerJoin(PlayerJoinEvent $event){
            $this->players[$event->getPlayer()->getName()] = 0;
    	}

        public function onRecieve(DataPacketReceiveEvent $event) {
            $player = $event->getPlayer();
            $packet = $event->getPacket();
            if ($packet instanceof AdventureSettingsPacket) {
                switch ($packet->flags) {
                    case 614:
                        if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight()){
                            var_dump("ไอสัส ".$player->getName()." Hack Fly");
                            $player->kick("ไอสัส HACK Fly");
                        }
                        break;
                    case 102:
                        if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight()){
                            var_dump("ไอสัส ".$player->getName()." Hack Fly");
                            $player->kick(TextFormat::RED."ไอสัส HACK Fly");
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        public function PlayerMove(PlayerMoveEvent $event){
            $player = $event->getPlayer();
            if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight() and !$player->hasEffect(Effect::JUMP)){
                if(round($event->getTo()->getY() - $event->getFrom()->getY(),3) >= 0.375 or round($event->getTo()->getY() - $event->getFrom()->getY(),3) === -0.375) {
                    $this->players[$name] ++;
                }else{
                    $this->players[$name] = 0;
                }
                if($this->players[$name] >= 3){
	                var_dump("ไอสัส ".$player->getName()." Hack โดดสูง หรือ Fly");
                    $player->kick(TextFormat::RED."ไอสัส Hack โดดสูง หรือ Fly");
	            }
            }
	    }
    }