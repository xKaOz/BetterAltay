<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\UpdateAbilitiesPacketLayer;

class UpdateAbilitiesPacket extends DataPacket{

	public const NETWORK_ID = ProtocolInfo::UPDATE_ABILITIES_PACKET;

	public int $commandPermission = CommandPermissions::NORMAL;
	public int $playerPermission = PlayerPermissions::MEMBER;
	public int $targetActorUniqueId;
	public array $abilityLayers = [];

	protected function decodePayload(){
		$this->targetActorUniqueId = $this->getLLong();
		$this->playerPermission = $this->getByte();
		$this->commandPermission = $this->getByte();
		$this->abilityLayers = [];
		for($i = 0, $len = $this->getByte(); $i < $len; $i++){
			$this->abilityLayers[] = UpdateAbilitiesPacketLayer::decode($this);
		}
	}

	protected function encodePayload(){
		$this->putLLong($this->targetActorUniqueId);
		$this->putByte($this->playerPermission);
		$this->putByte($this->commandPermission);
		$this->putByte(count($this->abilityLayers));
		foreach($this->abilityLayers as $layer){
			$layer->encode($this);
		}
	}

	public function fastEncode() : void{
		$this->encodePayload();
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateAbilities($this);
	}
}