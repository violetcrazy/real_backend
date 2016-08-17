<?php
namespace ITECH\Data\Model;

class ApartmentCeriterialModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $name;
    public $name_eng;
    public $attribute_type;
    public $attribute_view;
    public $attribute_utility;
    public $attribute_room_type;
    public $attribute_best_for;
    public $attribute_suitable_for;
    public $project_ids;
    public $bedroom_count;
    public $bathroom_count;
    public $total_area;
    public $green_area;
    public $price_min;
    public $price_max;
    public $price;
    public $direction;
    public $type;
    public $is_new;
    public $is_home;
    public $status;
    public $template;
    public $ordering;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;
    public $price_min_eng;
    public $price_max_eng;
    public $price_eng;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_apartment_ceriterial');
    }
}