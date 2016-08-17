<?php
namespace ITECH\Data\Model;

class LocationModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $parent_id;
    public $name;
    public $slug;
    public $ordering;
    public $project_count;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_location');
    }
}