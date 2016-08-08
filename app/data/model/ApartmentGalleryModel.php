<?php
namespace ITECH\Data\Model;

class ApartmentGalleryModel extends \ITECH\Data\Model\BaseModel
{
    public $id;
    public $apartment_id;
    public $name;
    public $name_eng;
    public $price;
    public $price_eng;
    public $gallery;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_apartment_gallery');

        $this->belongsTo('apartment_id', 'ITECH\Data\Model\ApartmentModel', 'id', array(
            'alias'      => 'Apartment',
            'foreignKey' => true
        ));
    }
}
