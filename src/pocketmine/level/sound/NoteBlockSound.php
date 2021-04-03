<?php

declare(strict_types=1);

namespace pocketmine\level\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class NoteBlockSound extends Sound {

	public const INSTRUMENT_PIANO = 0;
	public const INSTRUMENT_BASS_DRUM = 1;
	public const INSTRUMENT_CLICK = 2;
	public const INSTRUMENT_TABOUR = 3;
	public const INSTRUMENT_BASS = 4;
	public const INSTRUMENT_GLOCKENSPIEL = 5;
	public const INSTRUMENT_FLUTE = 6;
	public const INSTRUMENT_CHIME = 7;
	public const INSTRUMENT_GUITAR = 8;
	public const INSTRUMENT_XYLOPHONE = 9;
	public const INSTRUMENT_VIBRAPHONE = 10;
	public const INSTRUMENT_COW_BELL = 11;
	public const INSTRUMENT_DIDGERIDOO = 12;
	public const INSTRUMENT_SQUARE_WAVE = 13;
	public const INSTRUMENT_BANJO = 14;
	public const INSTRUMENT_ELECTRIC_PIANO = 15;

	/** @var int */
	protected $instrument = self::INSTRUMENT_PIANO;
	/** @var int */
	protected $note = 0;

	/**
	 * NoteBlockSound constructor.
	 */
	public function __construct(Vector3 $pos, int $instrument = self::INSTRUMENT_PIANO, int $note = 0) {
		parent::__construct($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ());

		$this->instrument = $instrument;
		$this->note = $note;
	}

	public function encode() {
		$pk = new BlockEventPacket();
		$pk->x = (int) $this->x;
		$pk->y = (int) $this->y;
		$pk->z = (int) $this->z;
		$pk->eventType = $this->instrument;
		$pk->eventData = $this->note;

		$pk2 = new LevelSoundEventPacket();
		$pk2->sound = LevelSoundEventPacket::SOUND_NOTE;
		//shut up stan???
		$pk2->position = $this->asVector3();
		$pk2->extraData = $this->instrument << 8 | $this->note;

		return [$pk, $pk2];
	}
}