<?php

    namespace ac;

    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use pocketmine\network\protocol\AdventureSettingsPacket;
    use pocketmine\event\server\DataPacketReceiveEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\entity\Effect;
    use pocketmine\utils\TextFormat;
    use pocketmine\network\protocol\UpdateAttributesPacket;

    class Main extends PluginBase implements Listener {

        #เอาไปใช้ดีก็ขอบคุณกันนิดนึง 
        #By TIGER OWNER APPLECRAFt

        #AppleCraft ip : applecraft.cf port : 19132

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
            if($packet instanceof UpdateAttributesPacket){ #กัน Player ส่ง Data AttributesPacket เพราะมันเกี่ยวกันเลือด อาหาร Speed การเดิม //พบคน Hack อาการนี้น้อย
                var_dump($player->getName()." Hack AttributesPacket");
                $player->kick("HACK AttributesPacket");
            }
            if ($packet instanceof AdventureSettingsPacket) { 
                switch ($packet->flags) { 
                    case 614: #กัน Fly ชั้นที่ 1 เช้ค packet ใช้ปุ่มลอย ไม่เตะมั่ว 100%
                        if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight()){
                            var_dump($player->getName()." Hack Fly");
                            $player->kick("HACK Fly");
                        }
                        break;
                    case 102: #กัน Fly ชั้นที่ 1 เช้ค packet ใช้ปุ่มลอย ไม่เตะมั่ว 100%
                        if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp() and !$player->getAllowFlight()){
                            var_dump($player->getName()." Hack Fly");
                            $player->kick(TextFormat::RED."HACK Fly");
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        public function PlayerMove(PlayerMoveEvent $event){
            $player = $event->getPlayer();
            if(!$player->isCreative() and !$player->isSpectator() and !$player->isOp()){
                if(!$player->hasEffect(Effect::SPEED)){ #กัน TapTelePort เตะทันทีที่วาป โอกาศ 80% กันได้ กัน Speed ได้แค่ 10% ไม่เตะมั่ว 100%
                    if(abs(round(($event->getTo()->getX() - $event->getFrom()->getX()) * ($event->getTo()->getZ() - $event->getFrom()->getZ()),3)) > 1){
                        var_dump($player->getName()." Hack Speed");
                        $player->kick(TextFormat::RED."Hack Speed");
                    }
                }
                if(!$player->getAllowFlight() and !$player->hasEffect(Effect::JUMP)){ #กัน โดดสูงๆ โอกาศป้องกันขั้นนี้ 90% ไม่เตะมั่ว 100%
                    if(round($event->getTo()->getY() - $event->getFrom()->getY(),3) === 0.375) {
                        $this->players[$player->getName()] ++;
                    }else{
                        $this->players[$player->getName()] = 0;
                    }
                    if($this->players[$player->getName()] >= 3){
                        var_dump($player->getName()." Hack HighJUMP or Fly");
                        $player->kick(TextFormat::RED."Hack HighJUMP or Fly");
                    }
                }
            }
	    }
    }