<?php
namespace ITECH\Data\Model;

class MediaTermModel extends \ITECH\Data\Model\BaseModel
{
    public $category_id;
    public $parent_id;
    public $name;
    public $counter_media;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_media_term');
    }
}
