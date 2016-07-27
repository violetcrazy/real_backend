<?php
namespace ITECH\Data\Model;

class ApartmentAttributeModel extends \ITECH\Data\Model\BaseModel
{
    public $apartment_id;
    public $attribute_id;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('land_apartment_attribute');

        $this->belongsTo('apartment_id', 'ITECH\Data\Model\ApartmentModel', 'id', array(
            'alias' => 'Apartment',
            'foreignKey' => true
        ));

        $this->belongsTo('attribute_id', 'ITECH\Data\Model\AttributeModel', 'id', array(
            'alias' => 'Attribute',
            'foreignKey' => true
        ));
    }
}