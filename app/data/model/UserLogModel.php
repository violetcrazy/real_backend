<?php
namespace ITECH\Data\Model;

class UserLogModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $user_id;
    public $action;
    public $referral_url;
    public $user_agent;
    public $ip;
    public $created_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_user_log');

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
    }
}