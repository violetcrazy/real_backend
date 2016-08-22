<?php
namespace ITECH\Data\Model;

class BannerModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $group_id;
    public $name;
    public $name_eng;
    public $slug;
    public $slug_eng;
    public $url;
    public $description;
    public $description_eng;
    public $click;
    public $image;
    public $status;
    public $ordering;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_banner');
    }
}
