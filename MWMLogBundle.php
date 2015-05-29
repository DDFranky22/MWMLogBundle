<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 03/04/2015
 * Time: 10.01
 */

namespace MWM\LogBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass;
use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;

class MWMLogBundle extends Bundle{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $modelDir = realpath(__DIR__.'/Resources/config/doctrine/model');
        $mappings = array(
            $modelDir => 'MWM\LogBundle\Model',
        );

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver(
                    $mappings,
                    array('mwm_log.model_manager_name'),
                    'mwm_log.backend_type_orm',
                    array('MWMLogBundle' => 'MWM\LogBundle\Model')
                ));
        }

        $mongoCompilerClass = 'Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass';
        if (class_exists($mongoCompilerClass)) {
            $container->addCompilerPass(
                DoctrineMongoDBMappingsPass::createXmlMappingDriver(
                    $mappings,
                    array('mwm_log.model_manager_name'),
                    'mwm_log.backend_type_mongodb',
                    array('MWMLogBundle' => 'MWM\LogBundle\Model')
                ));
        }

        $couchCompilerClass = 'Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass';
        if (class_exists($couchCompilerClass)) {
            $container->addCompilerPass(
                DoctrineCouchDBMappingsPass::createXmlMappingDriver(
                    $mappings,
                    array('mwm_log.model_manager_name'),
                    'mwm_log.backend_type_couchdb',
                    array('MWMLogBundle' => 'MWM\LogBundle\Model')
                ));
        }

        $phpcrCompilerClass = 'Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass';
        if (class_exists($phpcrCompilerClass)) {
            $container->addCompilerPass(
                DoctrinePhpcrMappingsPass::createXmlMappingDriver(
                    $mappings,
                    array('mwm_log.model_manager_name'),
                    'mwm_log.backend_type_phpcr',
                    array('MWMLogBundle' => 'MWM\LogBundle\Model')
                ));
        }
    }
}
