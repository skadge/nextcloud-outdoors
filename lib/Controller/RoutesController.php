<?php
namespace OCA\Outdoors\Controller;

use Exception;
use OCA\Outdoors\Db\RouteMapper;
use OCA\Outdoors\Service\RouteService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use Throwable;

class RoutesController extends OCSController {

	public function __construct(
		string             $appName,
		IRequest           $request,
		private RouteMapper $routeMapper,
		private RouteService $routeService,
		private ?string    $userId
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function getUserRoutes(): DataResponse {
		try {
			return new DataResponse($this->routeMapper->getRoutesOfUser($this->userId));
		} catch (Exception | Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $name
	 * @param string $content
	 * @return DataResponse
	 */
	public function addUserRoute(string $name, string $content = 'Type here a description'): DataResponse {
		try {
			$route= $this->routeMapper->createRoute($this->userId, $name, $content);
			return new DataResponse($route);
		} catch (Exception | Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 * @param string|null $name
	 * @param string|null $content
	 * @return DataResponse
	 */
	public function editUserRoute(int $id, ?string $name = null, ?string $content = null): DataResponse {
		try {
			$route = $this->routeMapper->updateRoute($id, $this->userId, $name, $content);
			return new DataResponse($route);
		} catch (Exception | Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 * @return DataResponse
	 */
	public function deleteUserRoute(int $id): DataResponse {
		try {
			$route = $this->routeMapper->deleteRoute($id, $this->userId);
			return new DataResponse($route);
		} catch (Exception | Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param int $id
	 * @return DataResponse
	 */
	public function exportUserRoute(int $id): DataResponse {
		try {
			$path = $this->routeService->exportRoute($id, $this->userId);
			return new DataResponse($path);
		} catch (Exception | Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}
}
