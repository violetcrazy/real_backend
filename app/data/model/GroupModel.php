<?php
namespace ITECH\Data\Model;

class GroupModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $name;
    public $name_eng;
    public $type;
    public $width;
    public $status;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_group');
    }
}