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
			foreach ($list as $key => $value) {
				var_dump($value["distance"]);
				if((int) $value["distance"] >= (int) 8.5){
					$this->instance->point[$key]["distance"]++;
					if((int) $this->instance->point[$key]["distance"] >= (int) 3){
						$player = $this->instance->getServer()->getPlayer($key);
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
?>