<?php
namespace ITECH\Admin\Form;

class ProjectMapImageForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $image_view = new \Phalcon\Forms\Element\Text('image_view');
        $image_view->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu upload hình ảnh lên.'
            ))
        ));
        $image_view->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($image_view);
        
        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getMapView());
        $this->add($type);
    }
}