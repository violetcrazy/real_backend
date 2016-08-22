<?php
namespace ITECH\Admin\Form;

class CategoryFengshuiForm extends \Phalcon\Forms\Form {
    public function initialize($model, $controller, $options = array()) {
        if ($model) {}
        if ($options) {}
        
        if ($controller) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập năm.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);
        
        $middle_name = new \Phalcon\Forms\Element\Text('middle_name');
        $middle_name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên gọi âm lịch của năm.'
            ))
        ));
        $middle_name->setFilters(array('striptags', 'trim'));
        $this->add($middle_name);

        $meta_title = new \Phalcon\Forms\Element\Text('meta_title');
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);

        $meta_keyword = new \Phalcon\Forms\Element\Textarea('meta_keyword');
        $meta_keyword->setFilters(array('striptags', 'trim'));
        $this->add($meta_keyword);

        $meta_description = new \Phalcon\Forms\Element\Textarea('meta_description');
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getCategoryStatus());
        $this->add($status);
    }
}