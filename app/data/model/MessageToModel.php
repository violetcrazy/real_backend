<?php
namespace ITECH\Data\Model;

class MessageToModel extends \ITECH\Data\Model\BaseModel
{
    public $message_id;
    public $user_id;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_message_to');

        $this->belongsTo('message_id', 'ITECH\Data\Model\MessageModel', 'id', array(
            'alias' => 'Message',
            'foreignKey' => true
        ));

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
    }
}