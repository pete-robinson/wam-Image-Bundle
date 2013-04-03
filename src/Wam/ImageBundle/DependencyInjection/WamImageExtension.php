<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */
 
namespace Wam\ImageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class WamImageExtension extends Extension
{

	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
		
		$loader = new XmlFileLoader($container, new FileLocator(array(__DIR__ . '/../Resources/config')));
		$loader->load('services.xml');

		foreach($config as $name => $value) {
			$container->setParameter('wam_' . $name, $value);
		}
	}


}