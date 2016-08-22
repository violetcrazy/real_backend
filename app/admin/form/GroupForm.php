<?php
namespace ITECH\Admin\Form;

class GroupForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options = array()) {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên nhóm.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name);
        
        $width = new \Phalcon\Forms\Element\Text('width');
        $width->setFilters(array('striptags', 'trim', 'int'));
        $this->add($width);
        
        $height = new \Phalcon\Forms\Element\Text('height');
        $height->setFilters(array('striptags', 'trim', 'int'));
        $this->add($height);

        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getGroupType());
        $this->add($type);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getGroupStatus());
        $this->add($status);
    }
}