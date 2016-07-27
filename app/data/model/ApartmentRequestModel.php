<?php
namespace ITECH\Data\Model;

class ApartmentRequestModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $user_id;
    public $agent_id;
    public $apartment_id;
    public $description;
    public $pay_method;
    public $status;
    public $approved_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_apartment_request');

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));

        $this->belongsTo('agent_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias' => 'Agent',
            'foreignKey' => true
        ));

        $this->belongsTo('apartment_id', 'ITECH\Data\Model\ApartmentModel', 'id', array(
            'alias' => 'Apartment',
            'foreignKey' => true
        ));
    }
}