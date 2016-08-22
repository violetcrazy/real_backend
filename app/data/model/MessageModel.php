<?php
namespace ITECH\Data\Model;

class MessageModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $name;
    public $phone;
    public $email;
    public $description;
    public $type;
    public $status_send;
    public $status_receive;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_message');
    }
}