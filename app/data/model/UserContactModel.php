<?php
namespace ITECH\Data\Model;

class UserContactModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $user_id;
    public $customer;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_user_contact');

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
    }
}