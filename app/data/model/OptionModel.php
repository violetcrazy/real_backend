<?php
namespace ITECH\Data\Model;

class OptionModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $key_option;
    public $value;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_option');
    }
}