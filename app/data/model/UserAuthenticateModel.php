<?php
namespace ITECH\Data\Model;

class UserAuthenticateModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $user_id;
    public $application;
    public $token;
    public $created_at;
    public $expired_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_user_authenticate');

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
    }
}