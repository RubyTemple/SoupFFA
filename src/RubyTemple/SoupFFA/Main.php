<?php

//By RubyTemple

namespace RubyTemple\SoupFFA;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\PopSound;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use function is_numeric;

class Main extends PluginBase implements Listener{
	/** @var float */
	private $hearts;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->hearts = $this->getConfig()->get('heart');
		if(!is_numeric($this->hearts)){
			$this->getLogger()->error("Config: \"heart\" must be a numeric value! The plugin will disable to avoid unexpected errors!");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function isValidWorld(Player $player) : bool{
		if($this->getConfig()->get('worlds') === 'all') return true;
		foreach($this->getConfig()->get('worlds') as $worlds){
			if($player->getLevel()->getFolderName() === $worlds){
				return true;
			}
		}

		return false;
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onInteract(PlayerInteractEvent $event) : void{
		$player = $event->getPlayer();
		if($event->isCancelled()) return;
		elseif($this->isValidWorld($player)){
			if($player->getInventory()->getItemInHand()->getId() === Item::MUSHROOM_STEW){
				if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK or $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
					$player->setHealth($player->getHealth() + $this->hearts * 2);
					$player->getLevel()->addSound(new PopSound($player));
					$player->getInventory()->setItemInHand(Item::get(Item::AIR));
				}
			}
		}
	}
}
