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
    use pocketmine\event\player\PlayerKickEvent;
    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\utils\TextFormat;
    use pocketmine\utils\UUID;
    use pocketmine\item\Item;
    use pocketmine\entity\Entity;
    use pocketmine\Player;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    use pocketmine\event\entity\EntityDamageEvent;

    class Main extends PluginBase implements Listener {

        public $movePlayers = [];

        public $point = [];

        public $npcs = [];

        public function onEnable() {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckTask($this), 20);
        }

        public function onPlayerKick(PlayerKickEvent $event){
            if($event->getReason() === "Sorry, hack mods are not permitted on Steadfast... at all."){
                //$event->setCancelled(true);
            }
    	}

        public function onDamage(EntityDamageEvent $event){
            if($event instanceof EntityDamageByEntityEvent and $event->getEntity() instanceof Player and $event->getDamager() instanceof Player){
                if($event->getEntity()->distanceSquared($event->getDamager()) >= 12){
                    $event->setCancelled();
                }
            }
        }

        public function onPlayerJoin(PlayerJoinEvent $event){
            $id = Entity::$entityCount++;
            $uuid = UUID::fromRandom();
            $player = $event->getPlayer();

            $pkadd = new AddPlayerPacket();
            $pkadd->uuid = $uuid;
            $pkadd->username = "";
            $pkadd->eid = $id;
            $pkadd->x = $player->x;
            $pkadd->y = $player->y - 2;
            $pkadd->z = $player->z;
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

            $this->npcs[$player->getName()]["add"] = $pkadd;
            $this->npcs[$player->getName()]["id"] = $id;
            $this->npcs[$player->getName()]["remove"] = $pkremove;
            $player->dataPacket($this->npcs[$player->getName()]["add"]);
            $this->movePlayers[$player->getName()]["distance"] = 0;
            $this->point[$player->getName()]["distance"] = 0;
    	}

        public function onPlayerQuit(PlayerQuitEvent $event){
            $player = $event->getPlayer();
            unset($this->movePlayers[$player->getName()]);
            unset($this->point[$player->getName()]);
            unset($this->npcs[$player->getName()]);
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