<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 03/04/2015
 * Time: 11.32
 */

namespace MWM\LogBundle\Model;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Debug\Exception\ContextErrorException;

abstract class Log implements LogInterface{
    /**
     * @var integer
     *
     */
    private $id;

    /**
     * @var \DateTime
     *
     */
    private $timelog;

    /**
     * @var string
     *
     */
    private $user;

    /**
     * @var array
     *
     */
    private $roleUser;

    /**
     * @var string
     *
     */
    private $operation;

    /**
     * @var string
     *
     */
    private $entityType;

    /**
     * @var array
     *
     */
    private $entityId;

    /**
     * @var array
     *
     */
    private $entityInfo;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set timelog
     *
     * @param \DateTime $timelog
     * @return Log
     */
    public function setTimelog($timelog)
    {
        $this->timelog = $timelog;

        return $this;
    }

    /**
     * Get timelog
     *
     * @return \DateTime
     */
    public function getTimelog()
    {
        return $this->timelog;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return Log
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set roleUser
     *
     * @param string $roleUser
     * @return Log
     */
    public function setRoleUser($roleUser)
    {
        $this->roleUser = $roleUser;

        return $this;
    }

    /**
     * Get roleUser
     *
     * @return string 
     */
    public function getRoleUser()
    {
        return $this->roleUser;
    }

    /**
     * Set operation
     *
     * @param string $operation
     * @return Log
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set entityType
     *
     * @param string $entityType
     * @return Log
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * Get entityType
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Set entityId
     *
     * @param array $entityId
     * @return Log
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return array
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entityInfo
     *
     * @param string $entityInfo
     * @return Log
     */
    public function setEntityInfo($entityInfo)
    {
        $this->entityInfo = $entityInfo;

        return $this;
    }

    /**
     * Get entityInfo
     *
     * @return string
     */
    public function getEntityInfo()
    {
        return $this->entityInfo;
    }

    public function retriveUserInfo($token){
        $user = "anon.";
        $roles = array('IS_AUTHENTICATED_ANONYMOUSLY');
        if($token!==null){
            $user = $token->getUser();
            $roles = $token->getRoles();
        }
        $this->setUser($user);
        $this->setRoleUser($roles);
    }


    public function retriveEntityInfo(ClassMetadataFactory $factory, $entity){
        $attributes = $factory->getMetadataFor(get_class($entity));
        $tmpSplit = explode("\\",$attributes->getName());
        $entityType = $tmpSplit[count($tmpSplit)-1];
        $this->setEntityType($entityType);
        $this->setEntityId($attributes->getIdentifierValues($entity));
        $reflectionProperties = $attributes->getReflectionProperties( );
        $properties = array();
        foreach($reflectionProperties as $property){
            try{
                $childEntity = $attributes->getFieldValue($entity,$property->getName());
                if($childEntity instanceof PersistentCollection){
                    $collection = array();
                    foreach($childEntity as $item){
                        $childAttributes = $factory->getMetadataFor(get_class($item));
                        $child = $childAttributes->getIdentifierValues($item);
                        $itemSplit = explode("\\",$childAttributes->getName());
                        $itemClass = $itemSplit[count($itemSplit)-1];
                        $collection[$itemClass.'_CI'] = $child;
                    }
                    $properties[$property->getName().'_C'] = $collection;
                }
                else{
                    $childAttributes = $factory->getMetadataFor(get_class($childEntity));
                    $child = $childAttributes->getIdentifierValues($childEntity);
                    $properties[$property->getName().'_AE'] = $child;
                }
            }
            catch(ContextErrorException $e){
                $val = $attributes->getFieldValue($entity,$property->getName());
                $properties[$property->getName()] = $val;
                continue;
            }
        }
        $this->setEntityInfo($properties);
    }

}
