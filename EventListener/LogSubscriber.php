<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 03/04/2015
 * Time: 11.16
 */

namespace MWM\LogBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use MWM\LogBundle\Model\Log;
use MWM\LogBundle\Model\LogInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class LogSubscriber
 *
 * Subscriber that listen to every possible doctrine event and create a log to persist on db
 *
 * @package MWM\LogBundle\EventListener
 */
class LogSubscriber implements EventSubscriber
{

    private $container;

    /**
     * @var TokenStorageInterface
     */
    private $token_storage;

    private $dbConnection;

    private $loggableEntities;

    private $logClass;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Service construct
     *
     * The construct of this service consider also all the loggable entities and alternative db connection.
     * If no array for $loggableEntities is passed, every entity will be logged.
     * $logClass is required.
     *
     * @param Container $container
     * @param array $loggableEntities
     * @param $dbConnection
     * @param $logClass
     */
    public function __construct(Container $container, array $loggableEntities, $dbConnection, $logClass)
    {
        $this->container = $container;
        $this->dbConnection = $dbConnection;
        $this->loggableEntities = array();
        if (count($loggableEntities) == 0) {
            $this->loggableEntities['all'] = true;
        } else {
            foreach ($loggableEntities as $entityStr) {
                array_push($this->loggableEntities, $entityStr);
            }
        }
        $this->logClass = $logClass;
    }

    public function retriveTokenStorage()
    {
        $this->token_storage = $this->container->get('security.token_storage');
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'postRemove',
            'prePersist',
            'postPersist',
            'preUpdate',
            'postUpdate',
            'postLoad',
            'loadClassMetadata',
            'onClassMetadataNotFound',
            'preFlush',
            'onFlush',
            'postFlush',
            'onClear',
        ];
    }


    /**
     * The preRemove event occurs for a given entity before the respective EntityManager remove operation for that entity is executed.
     * It is not called for a DQL DELETE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (!($entity instanceof LogInterface)) {
            if ($this->canILogThis($entity)) {
                $this->em = $eventArgs->getEntityManager($this->dbConnection);
                $this->createLog($entity, 'Remove');
            }
        }
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be invoked after the database delete operations.
     * It is not called for a DQL DELETE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
    }

    /**
     * The prePersist event occurs for a given entity before the respective EntityManager persist operation for that entity is executed.
     * It should be noted that this event is only triggered on initial persist of an entity (i.e. it does not trigger on future updates).
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations.
     * Generated primary key values are available in the postPersist event.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (!($entity instanceof LogInterface)) {
            if ($this->canILogThis($entity)) {
                $this->em = $eventArgs->getEntityManager($this->dbConnection);
                $this->createLog($entity, 'New');
            }
        }
    }

    /**
     * The preUpdate event occurs before the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (!($entity instanceof LogInterface)) {
            if ($this->canILogThis($entity)) {
                $this->em = $eventArgs->getEntityManager($this->dbConnection);
                $this->createLog($entity, 'Update');
            }
        }
    }

    /**
     * The postLoad event occurs for an entity after the entity has been loaded into the current EntityManager
     * from the database or after the refresh operation has been applied to it.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
    }

    /**
     * The loadClassMetadata event occurs after the mapping metadata for a class has been loaded from a mapping source (annotations/xml/yaml).
     * This event is not a lifecycle callback.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
    }

    /**
     * Loading class metadata for a particular requested class name failed.
     * Manipulating the given event args instance allows providing fallback metadata even when no actual metadata exists or could be found.
     * This event is not a lifecycle callback.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function onClassMetadataNotFound(LoadClassMetadataEventArgs $eventArgs)
    {
    }

    /**
     * The preFlush event occurs at the very beginning of a flush operation.
     * This event is not a lifecycle callback.
     *
     * @param PreFlushEventArgs $eventArgs
     */
    public function preFlush(PreFlushEventArgs $eventArgs)
    {
    }

    /**
     * The onFlush event occurs after the change-sets of all managed entities are computed.
     * This event is not a lifecycle callback.
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
    }

    /**
     * The postFlush event occurs at the end of a flush operation.
     * This event is not a lifecycle callback.
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
    }

    /**
     * The onClear event occurs when the EntityManager#clear() operation is invoked, after all references to entities have been removed from the unit of work.
     * This event is not a lifecycle callback.
     * @param OnClearEventArgs $eventArgs
     */
    public function onClear(OnClearEventArgs $eventArgs)
    {
    }


    /**
     * can I log this?
     *
     * Good question! This function check if the entity is inside the list of loggable entities.
     * (P.s. take a cookie! You've earned it!)
     *
     * @param $object
     * @return bool
     */
    private function canILogThis($object)
    {
        if (isset($this->loggableEntities['all'])) {
            return true;
        } else {
            try {
                foreach ($this->loggableEntities as $logEntity) {
                    if (is_a($object, $logEntity)) {
                        return true;
                    }
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Creation of the log and logging
     *
     * The main purpose of this function is to create the $log object based on the class defined by the user and to map the base fields.
     * Every function that retrive and extract info from the entity is on the Log class
     *
     * @param $entity
     * @param $operation
     * @throws EntityNotFoundException
     */
    private function createLog($entity, $operation)
    {
        $this->retriveTokenStorage();
        $em = $this->em;
        $logClass = $this->logClass;
        /** @var Log $log */
        $log = new $logClass();
        if ($log instanceof LogInterface) {
            $log->setTimelog(new \DateTime());
            $log->setOperation($operation);
            $log->retriveUserInfo($this->token_storage->getToken());
            $log->retriveEntityInfo($em->getMetadataFactory(), $entity);
            $em->persist($log);
            $em->flush();
        } else {
            throw new EntityNotFoundException;
        }
    }
}
