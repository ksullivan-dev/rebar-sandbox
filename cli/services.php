<?php
$c = require(__DIR__.'/../services.php');

/**
 * @param $c
 * @return \Monolog\Logger
 */
$c['logger'] = function ($c) {
	switch($c['config']['log']['clilevel']) {
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
	$logger->pushHandler(new \Monolog\Handler\StreamHandler($c['logPath'].'cli.log', $logLevel));
	return $logger;
};

return $c;
