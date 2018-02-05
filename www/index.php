<?php

namespace Sandbox;

use Fluxoft\Rebar\Auth\Exceptions\InvalidPasswordException;
use Fluxoft\Rebar\Auth\Exceptions\UserNotFoundException;
use Fluxoft\Rebar\Error\Handler;
use Fluxoft\Rebar\Exceptions\AuthenticationException;
use Fluxoft\Rebar\Exceptions\RouterException;
use Fluxoft\Rebar\Http\Environment;
use Fluxoft\Rebar\Http\Request;
use Fluxoft\Rebar\Http\Response;
use Fluxoft\Rebar\Route;
use Fluxoft\Rebar\Router;

$container = require_once 'services.php';

error_reporting(E_ALL);
ini_set('display_errors', 'on');

try {
	Handler::Handle(new Notifier(
		$container['logger']
	));
} catch (\Exception $e) {
	// all that can be done in this case is to just echo the exception
	header('HTTP/1.1 500 Unhandled exception');
	header('content-type: text/plain');
	echo "******************************\n";
	echo "***  Unhandled exception:  ***\n";
	echo "******************************\n";
	echo "\n";
	echo (string) $e;
	exit;
}

$router = new Router(
	$container['config']['app']['namespace'].'\Controllers',
	[$container]
);

// set auth for paths
// $router->SetAuthForPath($container['basicAuth'], '/util');
// $router->SetAuthForPath($container['basicAuth'], '/admin');

// custom routes
$router->AddRoute(
	new Route(
		'/sitemap.xml',
		'Main',
		'Sitemap'
	)
);

$request  = new Request(
	Environment::GetInstance()
);
$response = new Response();

try {
	$router->Route($request, $response);
} catch (RouterException $e) {
	$response->Status = 404;
	$response->AddHeader('content-type', 'text/plain');
	$response->Body  = "Resource not found.\n";
	$response->Body .= $e->getMessage();
	$response->Send();
} catch (AuthenticationException $e) {
	$response->Redirect('/auth/login');
} catch (UserNotFoundException $e) {
	$response->Halt(403, 'User not found.');
} catch (InvalidPasswordException $e) {
	$response->Halt(403, 'Incorrect password');
}
