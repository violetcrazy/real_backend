<?php
namespace ITECH\Data\Model;

class IconModel extends \ITECH\Data\Model\BaseModel
{
    public $icon_id;
    public $icon1;
    public $icon2;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_icon');
    }
}