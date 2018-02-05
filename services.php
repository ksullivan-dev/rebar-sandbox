<?php
require_once __DIR__.'/vendor/autoload.php';
$c = new Fluxoft\Rebar\Container();

$c['logPath'] = __DIR__.'/logs/';

$c['config'] = function () {
	return new \Fluxoft\Rebar\Config(__DIR__.'/config.ini');
};

$c['dbreader'] = function (\Fluxoft\Rebar\Container $c) {
	$connectionParams = [
		'dbname' => $c['config']['db']['reader']['name'],
		'user' => $c['config']['db']['reader']['user'],
		'password' => $c['config']['db']['reader']['pass'],
		'host' => $c['config']['db']['reader']['host'],
		'driver' => $c['config']['db']['reader']['driver'],
		'port' => $c['config']['db']['reader']['port']
	];
	$connection       = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
	return $connection;
};
$c['dbwriter'] = $c['dbreader']; // most of the time the same db connection will be used

$c['mapperFactory'] = function ($c) {
	return new \Sandbox\Mappers\MapperFactory($c['dbreader'], $c['dbwriter']);
};

return $c;
