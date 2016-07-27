<?php
namespace ITECH\Data\Model;

class ProjectModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $name;
    public $name_eng;
    public $slug;
    public $slug_eng;
    public $description;
    public $description_eng;
    public $address;
    public $address_eng;
    public $address_latitude;
    public $address_longitude;
    public $province_id;
    public $district_id;
    public $default_image;
    public $image_view;
    public $plan_view;
    public $gallery;
    public $block_count;
    public $apartment_count;
    public $available_count;
    public $processing_count;
    public $sold_count;
    public $direction;
    public $total_area;
    public $green_area;
    public $meta_title;
    public $meta_title_eng;
    public $meta_description;
    public $meta_description_eng;
    public $meta_keywords;
    public $meta_keywords_eng;
    public $view_count;
    public $status;
    public $created_by;
    public $updated_by;
    public $approved_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_project');
    }
}