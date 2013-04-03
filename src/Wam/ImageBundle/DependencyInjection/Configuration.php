<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {        
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('wam_image')
            ->children()
            		->scalarNode('tmp_dir')
                    ->defaultValue('%kernel.root_dir%/../web/tmp')
            	->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
