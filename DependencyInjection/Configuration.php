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

class Configuration implements ConfigurationInterface
{

    private $connection;

    public function __constructor($defaultConnection)
    {
        $this->connection = $defaultConnection;
        //adding this comment is becoming uncomfortable
    }

    public function getConfigTreeBuilder()
    {

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mwm_log');

        $supportedDrivers = array('orm', 'mongodb', 'couchdb', 'propel', 'custom');

        $rootNode
            ->children()
            ->scalarNode('log_class')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->variableNode('log_entities')
            ->defaultValue(array())
            ->end()
            ->scalarNode('db_driver')
            ->validate()
            ->ifNotInArray($supportedDrivers)
            ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode($supportedDrivers))
            ->end()
            ->cannotBeOverwritten()
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('db_connection')
            ->defaultValue($this->connection)
            ->end()
            ->scalarNode('model_manager_name')
            ->defaultNull()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
