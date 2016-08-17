<?php
namespace ITECH\Admin\Form;

class ProjectMapForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $image_view = new \Phalcon\Forms\Element\Text('image_view');
        $image_view->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu vẽ tọa độ.'
            ))
        ));
        $image_view->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($image_view);
        
        $block_id = new \Phalcon\Forms\Element\Select('block_id');
        $this->add($block_id);
    }
}