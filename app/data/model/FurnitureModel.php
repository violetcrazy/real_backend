<?php
namespace ITECH\Data\Model;

class FurnitureModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $name;
    public $name_eng;
    public $intro;
    public $intro_eng;
    public $email;
    public $phone;
    public $address;
    public $address_eng;
    public $logo;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_furniture');
    }
}