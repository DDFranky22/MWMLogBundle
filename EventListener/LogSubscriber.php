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
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use MWM\LogBundle\Entity\Log;
use MWM\LogBundle\Entity\LogInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogSubscriber implements EventSubscriber{

    private $token_storage;
    private $dbConnection;

    private $loggableEntities;

    /**
     * @param TokenStorageInterface $token_storage
     * @param array $loggableEntities
     * @param $dbConnection
     */
    public function __construct(TokenStorageInterface $token_storage, array $loggableEntities, $dbConnection){
        $this->token_storage = $token_storage;
        $this->dbConnection = $dbConnection;
        $this->loggableEntities = array();
        if(count($loggableEntities)==0){
            $this->loggableEntities['all'] = true;
        }
        else{
            foreach($loggableEntities as $entityStr){
                /*$tempEntity = new $entityStr();
                array_push($this->loggableEntities, $tempEntity);*/
                array_push($this->loggableEntities, $entityStr);
            }
        }
    }


    public function canILogThis($object){
        if(isset($this->loggableEntities['all'])){
            return true;
        }
        else{
            try{
                foreach($this->loggableEntities as $logEntity){
                    if(is_a($object, $logEntity)){
                        return true;
                    }
                }
            }
            catch(Exception $e){
                return false;
            }
        }
        return false;
    }

    public function getSubscribedEvents(){
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
    public function preRemove(LifecycleEventArgs $eventArgs){
        $entity = $eventArgs->getEntity();
        if(!($entity instanceof LogInterface)){
            if($this->canILogThis($entity)){
                $em = $eventArgs->getEntityManager($this->dbConnection);
                $this->createLog($em,$entity,'Remove');
            }
        }
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be invoked after the database delete operations.
     * It is not called for a DQL DELETE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs){
    }

    /**
     * The prePersist event occurs for a given entity before the respective EntityManager persist operation for that entity is executed.
     * It should be noted that this event is only triggered on initial persist of an entity (i.e. it does not trigger on future updates).
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs){
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations.
     * Generated primary key values are available in the postPersist event.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs){
        $entity = $eventArgs->getEntity();
        if(!($entity instanceof LogInterface)){
            if($this->canILogThis($entity)) {
                $em = $eventArgs->getEntityManager($this->dbConnection);
                $this->createLog($em, $entity, 'New');
            }
        }
    }

    /**
     * The preUpdate event occurs before the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs){
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs){
        $entity = $eventArgs->getEntity();
        if(!($entity instanceof LogInterface)){
            if($this->canILogThis($entity)) {
                $em = $eventArgs->getEntityManager($this->dbConnection);
                $this->createLog($em,$entity,'Update');
            }
        }
    }

    /**
     * The postLoad event occurs for an entity after the entity has been loaded into the current EntityManager
     * from the database or after the refresh operation has been applied to it.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs){
    }

    /**
     * The loadClassMetadata event occurs after the mapping metadata for a class has been loaded from a mapping source (annotations/xml/yaml).
     * This event is not a lifecycle callback.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs){
    }

    /**
     * Loading class metadata for a particular requested class name failed.
     * Manipulating the given event args instance allows providing fallback metadata even when no actual metadata exists or could be found.
     * This event is not a lifecycle callback.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function onClassMetadataNotFound(LoadClassMetadataEventArgs $eventArgs){
    }

    /**
     * The preFlush event occurs at the very beginning of a flush operation.
     * This event is not a lifecycle callback.
     *
     * @param PreFlushEventArgs $eventArgs
     */
    public function preFlush(PreFlushEventArgs $eventArgs){

    }

    /**
     * The onFlush event occurs after the change-sets of all managed entities are computed.
     * This event is not a lifecycle callback.
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs){
    }

    /**
     * The postFlush event occurs at the end of a flush operation.
     * This event is not a lifecycle callback.
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs){
    }

    /**
     * The onClear event occurs when the EntityManager#clear() operation is invoked, after all references to entities have been removed from the unit of work.
     * This event is not a lifecycle callback.
     * @param OnClearEventArgs $eventArgs
     */
    public function onClear(OnClearEventArgs $eventArgs){
    }

    private function createLog(EntityManager $em, $entity, $operation){
        $log = new Log();
        $log->setTimelog(new \DateTime());
        $log->setOperation($operation);
        $log->retriveUserInfo($this->token_storage->getToken());
        $log->retriveEntityInfo($em,$entity);
        $em->persist($log);
        $em->flush();
    }
}