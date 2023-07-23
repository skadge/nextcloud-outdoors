<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Séverin Lemaignan <severin@guakamole.org>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Outdoors\Db;


use DateTime;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCP\AppFramework\Db\DoesNotExistException;

class RouteMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'outdoors_routes', Route::class);
	}

	/**
	 * @param int $id
	 * @return Route
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getRoute(int $id): Route {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	public function getRouteByName(string $userId, string $name): Route {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)))
		        ->andWhere(
		  	        $qb->expr()->eq('name', $qb->createNamedParameter($name, IQueryBuilder::PARAM_STR)));

		return $this->findEntity($qb);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Route
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getRouteOfUser(int $id, string $userId): Route {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			)
			->andWhere(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @return Route[]
	 * @throws Exception
	 */
	public function getRoutesOfUser(string $userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntities($qb);
	}


	/**
	 * @param string $userId
	 * @param string $name
	 * @param string $content
	 * @return Route
	 * @throws Exception
	 */
	public function createRoute(string $userId, string $name, string $content): Route {
		$route = new Route();
		$route->setUserId($userId);
		$route->setName($name);
		$route->setContent($content);
		$timestamp = (new DateTime())->getTimestamp();
		$route->setLastModified($timestamp);
		return $this->insert($route);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @param string|null $name
	 * @param string|null $content
	 * @return Route|null
	 * @throws Exception
	 */
	public function updateRoute(int $id, string $userId, ?string $name = null, ?string $content = null): ?Route {
		if ($name === null && $content === null) {
			return null;
		}
		try {
			$route = $this->getRouteOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		if ($name !== null) {
			$route->setName($name);
		}
		if ($content !== null) {
			$route->setContent($content);
		}
		$timestamp = (new DateTime())->getTimestamp();
		$route->setLastModified($timestamp);
		return $this->update($route);
	}

	public function createOrUpdateRoute(string $userId, string $name = null, ?string $content = null): ?Route {
		try {
			$route = $this->getRouteByName($userId, $name);
		} catch (DoesNotExistException $e) {
			return $this->createRoute($userId, $name, $content);
		}

		if ($content !== null) {
			$route->setContent($content);
		}

		$timestamp = (new DateTime())->getTimestamp();
		$route->setLastModified($timestamp);
		return $this->update($route);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Route|null
	 * @throws Exception
	 */
	public function deleteRoute(int $id, string $userId): ?Route {
		try {
			$route = $this->getRouteOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($route);
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteRoutesOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}
}
