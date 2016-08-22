<?php
namespace ITECH\Data\Model;

class UserSaveModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $user_id;
    public $key;
    public $value;
    public $created_at;
    public $notify;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_user_save');

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias'      => 'User',
            'foreignKey' => true
        ));
    }
}
