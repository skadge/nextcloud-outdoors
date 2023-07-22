<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Séverin Lemaignan <severin@guakamole.org>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Outdoors\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

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

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('content', 'string');
		$this->addType('last_modified', 'integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'name' => $this->name,
			'content' => $this->content,
			'last_modified' => (int) $this->lastModified,
		];
	}
}
