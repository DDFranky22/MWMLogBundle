<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 12/05/2015
 * Time: 09.32
 */

namespace MWM\LogBundle\Model;


interface LogInterface
{

    //public function getId();

    public function getTimelog();

    public function setTimelog($timelog);

    public function getOperation();

    public function setOperation($operation);

}
