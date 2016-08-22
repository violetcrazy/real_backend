<?php
namespace ITECH\Data\Model;

class AttributeModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $name;
    public $name_eng;
    public $module;
    public $type;
    public $status;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_attribute');
    }
}
