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
					$player->dataPacket($npcs["remove"]);
					$npcs["add"]->x = $player->x; 
					$npcs["add"]->y = $player->y - 2; 
					$npcs["add"]->z = $player->z; 
					$player->dataPacket($npcs["add"]);
				}
				if((float) $value["distance"] > (float) 7.4){
					$this->instance->point[$key]["distance"] += (float) 1;
					if((float) $this->instance->point[$key]["distance"] > (float) 2){
						if($player instanceof Player){
							$player->kick(TextFormat::RED."#HACK Speed");
						}
					}
				} else {
					$this->instance->point[$key]["distance"] = (float) 0;
				}
				if((float) $value["fly"] > (float) 7.4){
					$this->instance->point[$key]["fly"] += (float) 1;
					if((float) $this->instance->point[$key]["fly"] > (float) 2){
						if($player instanceof Player){
							$player->kick(TextFormat::RED."#HACK Fly");
						}
					}
				} else {
					$this->instance->point[$key]["fly"] = (float) 0;
				}
				$this->instance->movePlayers[$key]["distance"] = (float) 0;
				$this->instance->movePlayers[$key]["fly"] = (float) 0;
			}
		}
	}