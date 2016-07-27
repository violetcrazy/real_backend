<?php
namespace ITECH\Data\Model;

class MapModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $map_image_id;
    public $item_id;
    public $point;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_map');
    }

    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            'field' => [
                'map_image_id',
                'item_id'
            ],
            'message' => 'Block - Khu này đã được MapLink'
        )));

        if ($this->validationHasFailed()) {
            return false;
        }

        return true;
    }
}