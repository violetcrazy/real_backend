<?php
namespace ITECH\Admin\Form;

class ApartmentGalleryForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);
        
        $price = new \Phalcon\Forms\Element\Text('price');
        $price->setFilters(array('striptags', 'trim'));
        $this->add($price);        
    }
}