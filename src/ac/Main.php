<?php

    namespace ac;

    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use pocketmine\network\protocol\AddPlayerPacket;
    use pocketmine\network\protocol\RemoveEntityPacket;
    use pocketmine\network\protocol\AdventureSettingsPacket;
    use pocketmine\network\protocol\UpdateAttributesPacket;
    use pocketmine\event\server\DataPacketReceiveEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerQuitEvent;
    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\utils\TextFormat;
    use pocketmine\utils\UUID;
    use pocketmine\item\Item;
    use pocketmine\entity\Entity;
    use pocketmine\Player;
    use pocketmine\math\Vector3;
    use pocketmine\block\Block;

    class Main extends PluginBase implements Listener {

        public $movePlayers = [];

        public $point = [];

        public $npcs = [];

        public function onEnable() {
            $id = Entity::$entityCount++;
            $uuid = UUID::fromRandom();

            $pkadd = new AddPlayerPacket();
            $pkadd->uuid = $uuid;
            $pkadd->username = "";
            $pkadd->eid = $id;
            $pkadd->x = 0;
            $pkadd->y = 0;
            $pkadd->z = 0;
            $pkadd->yaw = 0;
            $pkadd->pitch = 0;
            $pkadd->item = Item::fromString(0);;
            $flags = 0;
            $flags |= 1 << 5;
            $flags |= 1 << 14;
            $flags |= 1 << 15;
            $flags |= 1 << 16;
            $pkadd->metadata = [ 
                Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
                Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""]
            ];

            $pkremove = new RemoveEntityPacket();
            $pkremove->eid = $id;

            $this->npcs["add"] = $pkadd;
            $this->npcs["id"] = $id;
            $this->npcs["remove"] = $pkremove;

            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckTask($this), 20);
        }

        public function onDamage(EntityDamageEvent $event){
            if($event instanceof EntityDamageByEntityEvent && $event->getEntity() instanceof Player && $event->getDamager() instanceof Player && $event->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
                if(!$event->isCancelled()){
		    if(round($event->getEntity()->distanceSquared($event->getDamager())) >= 12){
                        $event->setCancelled();
                    }
                }
            }
        }

        public function onPlayerJoin(PlayerJoinEvent $event){
            $player = $event->getPlayer();
            $this->movePlayers[$player->getName()]["distance"] = (float) 0;
            $this->point[$player->getName()]["distance"] = (float) 0;
            $this->movePlayers[$player->getName()]["fly"] = (float) 0;
            $this->point[$player->getName()]["fly"] = (float) 0;
    	}

        public function onPlayerQuit(PlayerQuitEvent $event){
            $player = $event->getPlayer();
            unset($this->movePlayers[$player->getName()]);
            unset($this->point[$player->getName()]);
    	}

        public function onPlayerMove(PlayerMoveEvent $event){
            $player = $event->getPlayer();
            $oldPos = $event->getFrom();
	    $newPos = $event->getTo();
            if(!$player->isCreative() && !$player->isSpectator() && !$player->isOp() && !$player->getAllowFlight()){
                $FlyMove = (float) round($newPos->getY() - $oldPos->getY(),3);
                $DistanceMove = (float) round(sqrt(($newPos->getX() - $oldPos->getX()) ** 2 + ($newPos->getZ() - $oldPos->getZ()) ** 2),2);
                if($FlyMove === (float) -0.002 || $FlyMove === (float) -0.003){
                    $this->movePlayers[$player->getName()]["distance"] += 3;
                }
                $this->movePlayers[$player->getName()]["fly"] += $FlyMove;
                $this->movePlayers[$player->getName()]["distance"] += $DistanceMove;
            }
    	}

        public function onRecieve(DataPacketReceiveEvent $event) {
            $player = $event->getPlayer();
            $packet = $event->getPacket();
            if($packet instanceof UpdateAttributesPacket){ 
                $player->kick(TextFormat::RED."#HACK UpdateAttributesPacket");
		return;
            }
            if($packet instanceof SetPlayerGameTypePacket){ 
                $player->kick(TextFormat::RED."#HACK SetPlayerGameTypePacket");
		return;
            }
            if($packet instanceof AdventureSettingsPacket){
                if(!$player->isCreative() && !$player->isSpectator() && !$player->isOp() && !$player->getAllowFlight()){
                    switch ($packet->flags){ //Packet ส่งขอลอย
                        case 614:
                        case 615:
                        case 103:
                        case 102:
                        case 38:
                        case 39:
                            $player->kick(TextFormat::RED."#HACK Fly and NoClip");
			    return;
                            break;
                        default:
                            break;
                    }
                    if((($packet->flags >> 9) & 0x01 === 1) || (($packet->flags >> 7) & 0x01 === 1) || (($packet->flags >> 6) & 0x01 === 1)){
                        $player->kick(TextFormat::RED."#HACK Fly and NoClip");
			return;
                    }
                }
            }
        }
    }
