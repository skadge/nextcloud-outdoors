<?php
declare(strict_types=1);

$requirements = [
	'apiVersion' => 'v1',
];

// SPDX-FileCopyrightText: Séverin Lemaignan <severin@guakamole.org>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Outdoors\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
//	'resources' => [
//		'note' => ['url' => '/notes'],
//		'note_api' => ['url' => '/api/0.1/notes']
//	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'config#setConfig', 'url' => '/config', 'verb' => 'PUT'],
//		['name' => 'note_api#preflighted_cors', 'url' => '/api/0.1/{path}',
//			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	],
	'ocs' => [
		['name' => 'routes#getUserRoutes', 'url' => '/api/{apiVersion}/routes', 'verb' => 'GET', 'requirements' => $requirements],
		['name' => 'routes#exportUserRoute', 'url' => '/api/{apiVersion}/routes/{id}/export', 'verb' => 'GET', 'requirements' => $requirements],
		['name' => 'routes#addUserRoute', 'url' => '/api/{apiVersion}/routes', 'verb' => 'POST', 'requirements' => $requirements],
		['name' => 'routes#editUserRoute', 'url' => '/api/{apiVersion}/routes/{id}', 'verb' => 'PUT', 'requirements' => $requirements],
		['name' => 'routes#deleteUserRoute', 'url' => '/api/{apiVersion}/routes/{id}', 'verb' => 'DELETE', 'requirements' => $requirements],
		['name' => 'routes#getUserGpxRoutes', 'url' => '/api/{apiVersion}/routes/gpx', 'verb' => 'GET', 'requirements' => $requirements],
	],
];
