<?php

    namespace ac;

    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use pocketmine\network\protocol\AdventureSettingsPacket;
    use pocketmine\network\protocol\SetPlayerGameTypePacket;
    use pocketmine\event\server\DataPacketReceiveEvent;

    class Main extends PluginBase implements Listener {

        public function onEnable() {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
        }

        public function onRecieve(DataPacketReceiveEvent $event) {
            $player = $event->getPlayer();
            $packet = $event->getPacket();
            var_dump($packet->pid());
            if ($packet instanceof SetPlayerGameTypePacket){
                var_dump("FUCK");
            }
            if ($packet instanceof AdventureSettingsPacket) {
                $event->setCancelled();
                switch ($packet->flags) {
                    case 614:
                        var_dump("FUCK");
                        if(!$player->isCreative() and !$player->isSpectator()){
                            var_dump("ไอสัส Hack ลอยขึ้น");
                        }
                        break;
                    case 102:
                        if(!$player->isCreative() and !$player->isSpectator()){
                            var_dump("ไอสัส Hack ลอยลง");
                        }
                        break;
                    default:
                        # code...
                        break;
                }
                var_dump($packet->flags);
            }
        }
    }