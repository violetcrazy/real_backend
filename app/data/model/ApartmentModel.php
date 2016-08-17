<?php
namespace ITECH\Data\Model;

class ApartmentModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $user_id;
    public $block_id;
    public $name;
    public $name_eng;
    public $slug;
    public $slug_eng;
    public $description;
    public $description_eng;
    public $condition;
    public $type;
    public $price;
    public $price_eng;
    public $price_sale_off;
    public $price_sale_off_eng;
    public $default_image;
    public $panorama_image;
    public $gallery;
    public $position;
    public $position_eng;
    public $floor;
    public $room_count;
    public $bedroom_count;
    public $bathroom_count;
    public $adults_count;
    public $children_count;
    public $direction;
    public $total_area;
    public $green_area;
    public $rose;
    public $ordering;
    public $view_count;
    public $status;
    public $created_at;
    public $updated_at;
    public $furniture_id;
    public $meta_title;
    public $meta_title_eng;
    public $meta_keywords;
    public $meta_keywords_eng;
    public $meta_description;
    public $meta_description_eng;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_apartment');

        $this->belongsTo('user_id', 'ITECH\Data\Model\UserModel', 'id', array(
            'alias'    => 'User',
            'reusable' => true
        ));

        $this->belongsTo('block_id', 'ITECH\Data\Model\BlockModel', 'id', array(
            'alias'      => 'Block',
            'foreignKey' => true
        ));
    }
}
