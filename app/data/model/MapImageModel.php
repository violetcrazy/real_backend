<?php
namespace ITECH\Data\Model;

class MapImageModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $item_id;
    public $type;
    public $floor;
    public $image;
    public $module;
    public $position;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_map_image');
    }

    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            'field' => [
                'id',
                'item_id',
                'type',
                'floor',
                'module',
                'position'
            ],
            'message' => 'DUPLICATED'
        )));

        if ($this->validationHasFailed()) {
            return false;
        }

        return true;
    }
}