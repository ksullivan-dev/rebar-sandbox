<?php

namespace Sandbox\Controllers;

use Fluxoft\Rebar\Container;
use Fluxoft\Rebar\Controller;
use Psr\Log\LoggerInterface;

class Main extends Controller {
	/** @var Container */
	private $c;
	public function Setup(Container $container) {
		$this->c = $container;
	}

	public function Index() {
		$this->set('one', 'two');
	}
}
