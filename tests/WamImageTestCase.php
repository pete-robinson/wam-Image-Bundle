<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */
 
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WamImageTestCase extends WebTestCase
{

	/**
	 * client
	 * @var Symfony\Bundle\FrameworkBundle\Client
	 **/
	protected $client;

	/**
	 * Container
	 * @var object
	 **/
	protected $container;

	/**
	 * Application
	 * @var object
	 **/
	protected $application;

	/**
	 * setUp
	 *
	 * @return void
	 **/
	public function setUp()
	{
		parent::setUp();

		if(!isset($_ENV['CACHE_CLEARED']) || $_ENV['CACHE_CLEARED'] == false) {
			`rm -R tmp/cache/*`;
			$_ENV['CACHE_CLEARED'] = true;
		}

		$this->client = static::createClient();

		$kernel = static::createKernel();
		$kernel->boot();

		$this->container = $kernel->getContainer();

		$this->application = new Application($kernel);
		$this->application->setAutoExit(false);
	}

}