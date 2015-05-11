<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 11/05/2015
 * Time: 13.27
 */

namespace MWM\LogBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LogExtension extends Extension{

    public function load(array $configs, ContainerBuilder $container){

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }

}