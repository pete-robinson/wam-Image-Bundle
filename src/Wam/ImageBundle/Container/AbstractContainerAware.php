<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */
 
namespace Wam\ImageBundle\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractContainerAware implements ContainerAwareInterface
{
    /**
     * Service Container Interface
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $serviceContainer;

    /**
     * Set the service container
     * @param ContainerInterface $container 
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->serviceContainer = $container;
    }

    /**
     * Get the serice container
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->serviceContainer;
    }
}