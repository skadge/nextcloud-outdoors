<?php

namespace OCA\Outdoors\Controller;

// use OCA\Outdoors\Db\NoteMapper;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\Collaboration\Reference\RenderReferenceEvent;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Controller;

use OCA\Outdoors\AppInfo\Application;
use OCA\Outdoors\Db\RouteMapper;

use OCP\PreConditionNotMetException;

use OCP\AppFramework\Http\ContentSecurityPolicy;

class PageController extends Controller {

	public function __construct(
		string   $appName,
		IRequest $request,
		private IEventDispatcher $eventDispatcher,
		private IInitialState $initialStateService,
		private IConfig $config,
		private RouteMapper $routeMapper,
		private ?string $userId
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 * @throws PreConditionNotMetException
	 */
	public function index(): TemplateResponse {
		$this->eventDispatcher->dispatchTyped(new RenderReferenceEvent());
 		try {
 			$routes = $this->routeMapper->getRoutesOfUser($this->userId);
 		} catch (\Exception | \Throwable $e) {
 			$routes = [];
 		}
		$selectedRouteId = (int) $this->config->getUserValue($this->userId, Application::APP_ID, 'selected_route_id', '0');
		$state = [
			'routes' => $routes,
			'selected_route_id' => $selectedRouteId,
		];
		$this->initialStateService->provideInitialState('outdoors-initial-state', $state);

		$response = new TemplateResponse(Application::APP_ID, 'main');

		$csp = new ContentSecurityPolicy();
		$csp->addAllowedImageDomain('https://*.tile.openstreetmap.org');
		$csp->addAllowedImageDomain('https://*.tile.opentopomap.org');

		$response->setContentSecurityPolicy($csp);

		return $response;

	}
}
