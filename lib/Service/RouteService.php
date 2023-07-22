<?php

namespace OCA\Outdoors\Service;

use Exception;
use OC\User\NoUserException;
use OCA\Outdoors\AppInfo\Application;
use OCA\Outdoors\Db\RouteMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Files\Folder;
use OCP\Files\File;
use OCP\Files\GenericFileException;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Lock\LockedException;

class RouteService {

	public function __construct(
		string $appName,
		private IRootFolder $rootFolder,
		private RouteMapper $routeMapper
	) {
	}

	/**
	 * @param string $userId
	 * @return Folder
	 * @throws NotPermittedException
	 * @throws NotFoundException
	 * @throws NoUserException
	 */
	private function createOrGetRoutesDirectory(string $userId): Folder {
		$userFolder = $this->rootFolder->getUserFolder($userId);
		if ($userFolder->nodeExists(Application::ROUTES_FOLDER_NAME)) {
			$node = $userFolder->get(Application::ROUTES_FOLDER_NAME);
			if ($node instanceof Folder) {
				return $node;
			}
			throw new Exception('/' . Application::ROUTES_FOLDER_NAME . ' exists and is not a directory');
		} else {
			return $userFolder->newFolder(Application::ROUTES_FOLDER_NAME);
		}
	}

	/**
	 * @param int $routeId
	 * @param string $userId
	 * @return string
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @throws NoUserException
	 * @throws NotFoundException
	 * @throws NotPermittedException
	 * @throws \OCP\DB\Exception
	 * @throws GenericFileException
	 * @throws LockedException
	 */
	public function exportRoute(int $routeId, string $userId): string {
		$routeFolder = $this->createOrGetRoutesDirectory($userId);
		$route = $this->routeMapper->getRouteOfUser($routeId, $userId);
		$fileName = $route->getName() . '.txt';
		$fileContent = $route->getContent();
		if ($routeFolder->nodeExists($fileName)) {
			$node = $routeFolder->get($fileName);
			if ($node instanceof File) {
				$node->putContent($fileContent);
				return Application::ROUTES_FOLDER_NAME . '/' . $fileName;
			}
			throw new Exception('/' . Application::ROUTES_FOLDER_NAME . '/' . $fileName .' exists and is not a file');
		} else {
			$routeFolder->newFile($fileName, $fileContent);
			return Application::ROUTES_FOLDER_NAME . '/' . $fileName;
		}
	}
}
