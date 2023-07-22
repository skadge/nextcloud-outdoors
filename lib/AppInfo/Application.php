<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Séverin Lemaignan <severin@guakamole.org>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Outdoors\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

class Application extends App implements IBootstrap {

	public const APP_ID = 'outdoors';
	public const ROUTES_FOLDER_NAME = 'Outdoors routes';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
	}

	public function boot(IBootContext $context): void {
	}
}
