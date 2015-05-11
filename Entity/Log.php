<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 03/04/2015
 * Time: 11.32
 */

namespace MWM\LogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="MWM\LogBundle\Repository\LogRepository")
 */

class Log {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_log", type="datetime", nullable=true)
     */
    private $timelog;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @var array
     *
     * @ORM\Column(name="role_user", type="json_array", length=4294967295, nullable=true)
     */
    private $roleUser;

    /**
     * @var string
     *
     * @ORM\Column(name="operation", type="string", length=255, nullable=true)
     */
    private $operation;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_type", type="string", length=255, nullable=true)
     */
    private $entityType;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_id", type="string", length=255, nullable=true)
     */
    private $entityId;

    /**
     * @var array
     *
     * @ORM\Column(name="entity_info", type="json_array", nullable=true)
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
     * @param string $entityId
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
     * @return string 
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
}
