<?php
namespace ITECH\Data\Model;

class BlockModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $project_id;
    public $name;
    public $name_eng;
    public $slug;
    public $slug_eng;
    public $shortname;
    public $default_image;
    public $gallery;
    public $description;
    public $description_eng;
    public $price;
    public $price_eng;
    public $policy;
    public $policy_eng;
    public $floor_name_list;
    public $floor_count;
    public $apartment_name_list;
    public $apartment_count;
    public $direction;
    public $total_area;
    public $green_area;
    public $view_count;
    public $status;
    public $created_by;
    public $created_at;
    public $updated_at;
    public $meta_title;
    public $meta_title_eng;
    public $meta_keywords;
    public $meta_keywords_eng;
    public $meta_description;
    public $meta_description_eng;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_block');

        $this->belongsTo('project_id', 'ITECH\Data\Model\ProjectModel', 'id', array(
            'alias' => 'Project',
            'foreignKey' => true
        ));
    }
}