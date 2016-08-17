<?php
namespace ITECH\Data\Model;

class CategoryModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $parent_id;
    public $name;
    public $name_eng;
    public $middle_name;
    public $middle_name_eng;
    public $slug;
    public $slug_eng;
    public $icon;
    public $image;
    public $banner;
    public $meta_title;
    public $meta_title_eng;
    public $meta_description;
    public $meta_description_eng;
    public $meta_keyword;
    public $meta_keyword_eng;
    public $status;
    public $ordering;
    public $module;
    public $article_count;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_category');
    }
}