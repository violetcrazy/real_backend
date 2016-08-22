<?php
namespace ITECH\Data\Model;

class BaseModel extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->setWriteConnectionService('db');
        $this->setReadConnectionService('db_slave');
    }

    public function setWriteConnection()
    {
        $this->setWriteConnectionService('db');
        $this->setReadConnectionService('db');
    }
}