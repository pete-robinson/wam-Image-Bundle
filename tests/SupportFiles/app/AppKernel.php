<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Acme\TestBundle\AcmeTestBundle(),
            new Wam\AssetBundle\WamAssetBundle(),
            new Wam\ImageBundle\WamImageBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');

    }

    public function getCacheDir()
    {
        return $this->rootDir . '/../../../tmp/cache';
    }

    public function getLogDir()
    {
        return $this->rootDir . '/../../../tmp/logs';
    }
}
