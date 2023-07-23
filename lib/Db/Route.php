<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Séverin Lemaignan <severin@guakamole.org>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Outdoors\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

use phpGPX\phpGPX;

/**
 * @method getId(): int
 * @method getTitle(): string
 * @method setTitle(string $title): void
 * @method getContent(): string
 * @method setContent(string $content): void
 * @method getUserId(): string
 * @method setUserId(string $userId): void
 */
class Route extends Entity implements JsonSerializable {
	protected string $name = '';
	protected string $content = '';
	protected string $userId = '';
	protected $lastModified;
	private phpGPX $gpx;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('content', 'string');
		$this->addType('last_modified', 'integer');
	}
//
//	public function setContent(string $content): void {
//		$this->content = $content;
//		$this->gpx->parse($content);
//	}
//
	public function gpx(): ?phpGPX {
		if ($this->content === null) {
			return null;
		}

		$this->gpx = new phpGPX();
		$this->gpx->parse($this->content);

		return $this->gpx;
	}

	public function description(): string {
		if ($this->gpx() !== null) {
			$desc = "";
			if ($this->gpx->metadata->description !== null) {
				 $desc = $this->gpx->metadata->description;
			}
			if ($this->gpx->metadata->name !== null) {
				return ($this->gpx->metadata->name).($desc ? " (".$desc.")":"");
			}
			if ($desc) {
				return $desc;
			}
		}
		return $this->name;


	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'name' => $this->name,
			'description' => $this->description(),
			'content' => $this->content,
			'last_modified' => (int) $this->lastModified,
		];
	}
}
