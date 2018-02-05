<?php
$c = require('../services.php');

$c['cookies'] = function ($c) {
	return new \Fluxoft\Rebar\Http\Cookies(
		[
			'expires' => strtotime('+1 days'),
			'path' => $c['config']['auth']['cookiePath'],
			'domain' => $c['config']['auth']['cookieDomain']
		]
	);
};

$c['session'] = function () {
	return new \Fluxoft\Rebar\Http\Session();
};

$c['json'] = function () {
	return new \Fluxoft\Rebar\Presenters\Json();
};
$c['templatePath'] = __DIR__.'/../templates/';
$c['cachePath']    = __DIR__.'/../cache/';
$c['twig']         = function ($c) {
	$cache = $c['cachePath'] . 'Twig/cache';
	$debug = true;

	$env = $c['config']['app']['env'];
	if ($env === 'local' || $env === 'dev') {
		$cache = false;
	}
	return new \Fluxoft\Rebar\Presenters\Twig(
		$c['templatePath'],
		$cache,
		'main/index.twig',
		'',
		$debug
	);
};

$c['webAuth'] = function ($c) {
	/** @var \Sandbox\Mappers\MapperFactory $mapperFactory */
	$mapperFactory = $c['mapperFactory'];
	/** @var \Fluxoft\Rebar\Auth\Db\UserMapper $userMapper */
	$userMapper = $mapperFactory->Build('UserMapper');
	return new \Sandbox\Auth\Web(
		$userMapper,
		$c['cookies'],
		$c['session']
	);
};
$c['basicAuth'] = function ($c) {
	return new \Sandbox\Auth\Basic(
		new \Sandbox\Mappers\BasicUserMapper(),
		'Sandbox',
		'Mr. T says, "I pity the fool who don\'t log in!"'
	);
};

/**
 * @param $c
 * @return \Monolog\Logger
 */
$c['logger'] = function ($c) {
	switch($c['config']['log']['weblevel']) {
		case 'debug':
			$logLevel = \Monolog\Logger::DEBUG;
			break;
		case 'info':
			$logLevel = \Monolog\Logger::INFO;
			break;
		case 'warning':
			$logLevel = \Monolog\Logger::WARNING;
			break;
		case 'error':
			$logLevel = \Monolog\Logger::ERROR;
			break;
		case 'critical':
			$logLevel = \Monolog\Logger::CRITICAL;
			break;
		case 'alert':
			$logLevel = \Monolog\Logger::ALERT;
			break;
		case 'emergency':
			$logLevel = \Monolog\Logger::EMERGENCY;
			break;
		default:
			$logLevel = \Monolog\Logger::EMERGENCY;
			break;
	}
	$logger = new \Monolog\Logger('sandbox');
	$logger->pushHandler(new \Monolog\Handler\StreamHandler($c['logPath'].'web.log', $logLevel));
	return $logger;
};

return $c;
