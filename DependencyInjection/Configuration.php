<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 11/05/2015
 * Time: 13.45
 */

namespace MWM\LogBundle\DependencyInjection;



use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface{

    public function getConfigTreeBuilder(){

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mwm_log');

        $rootNode
            ->children()
                ->scalarNode('connection')->end();

        return $treeBuilder;
    }

}