<?php

namespace Sandbox;

use Fluxoft\Rebar\Error\NotifierInterface;
use Psr\Log\LoggerInterface;

class Notifier implements NotifierInterface {
	/** @var LoggerInterface */
	protected $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	/**
	 * Should be overridden in a Notifier class to accept an unhandled exception and do
	 * something with it. These classes should be very careful to handle all possible
	 * exceptions of their own in a graceful way so as not to cause a
	 * @param \Exception $e
	 * @return mixed
	 */
	public function Notify(\Exception $e) {
		try {
			$this->logger->critical('Uncaught exception', [
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
				'line' => $e->getLine(),
				'file' => $e->getFile(),
				'trace' => $e->getTraceAsString()
			]);
			throw $e;
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
	}
}
