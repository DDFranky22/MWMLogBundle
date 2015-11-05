<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 11/05/2015
 * Time: 13.27
 */

namespace MWM\LogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MWMLogExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {

        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $container->setParameter('mwm_log.log_class', $config['log_class']);

        $container->setParameter('mwm_log.db_connection', $config['db_connection']);

        $container->setParameter('mwm_log.log_entities', $config['log_entities']);

        $container->setParameter('mwm_log.model_manager_name', $config['model_manager_name']);

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.yml', $config['db_driver']));
            $container->setParameter($this->getAlias() . '.backend_type_' . $config['db_driver'], true);
        }
    }

    public function getAlias()
    {
        return 'mwm_log';
    }


    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('doctrine.default_connection'));
    }

}
