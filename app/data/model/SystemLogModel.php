<?php
namespace ITECH\Data\Model;

class SystemLogModel extends BaseModel
{
    public $id;
    public $userId;
    public $itemId;
    public $itemType;
    public $action;
    public $ip;
    public $createdAt;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_system_log');

        $this->belongsTo('userId', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
    }
}
