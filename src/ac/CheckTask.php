<?php 
	namespace ac;

	use pocketmine\scheduler\PluginTask;
	use pocketmine\utils\TextFormat;
    use pocketmine\Player;

	Class CheckTask extends PluginTask {

		private $instance;

		public function __construct(Main $plugin){
			parent::__construct($plugin);
			$this->instance = $plugin;
		}

		public function onRun($tick){
			$list = $this->instance->movePlayers;
			$npcs = $this->instance->npcs;
			
			foreach ($list as $key => $value) {
				$player = $this->instance->getServer()->getPlayer($key);
				if($player instanceof Player){
					$player->dataPacket($npcs[$player->getName()]["remove"]);
					$npcs[$player->getName()]["add"]->x = $player->x; 
					$npcs[$player->getName()]["add"]->y = $player->y - 2; 
					$npcs[$player->getName()]["add"]->z = $player->z; 
					$player->dataPacket($npcs[$player->getName()]["add"]);
				}
				if((int) $value["distance"] >= (int) 8.5){
					$this->instance->point[$key]["distance"]++;
					if((int) $this->instance->point[$key]["distance"] >= (int) 3){
						if($player instanceof Player){
							$player->kick(TextFormat::RED."#HACK Speed");
						}
					}
				} else {
					$this->instance->movePlayers[$key]["distance"] = 0;
				}
			}
		}
	}