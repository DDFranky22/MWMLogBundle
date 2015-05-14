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

    private $connection;

    public function __constructor($defaultConnection){
        $this->connection = $defaultConnection;
    }

    public function getConfigTreeBuilder(){

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mwm_log');
        $rootNode
            ->children()
                ->variableNode('log_entities')
                    ->defaultValue(array())
                ->end()
                ->scalarNode('db_connection')
                    ->defaultValue($this->connection)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}