<?php

namespace Sandbox\Controllers;

use Fluxoft\Rebar\Container;
use Fluxoft\Rebar\Rest\Controller;
use Sandbox\Repositories\TypesRepository;

class V1 extends Controller {
	/** @var Container */
	private $c;
	public function Setup(Container $container) {
		$this->c                         = $container;
		$this->presenter                 = $this->c['json'];
		$this->crossOriginEnabled        = true;
		$this->crossOriginDomainsAllowed = [
			'localhost'
		];
	}

	public function Index() {
		$this->set('index', 'here');
	}

	public function Types() {
		/** @var \Sandbox\Mappers\MapperFactory $mapperFactory */
		$mapperFactory = $this->c['mapperFactory'];
		$repository    = new TypesRepository(
			$mapperFactory->Build('TypeMapper'),
			$this->c['logger']
		);
		$this->handleRepository($repository, func_get_args());
	}
}
