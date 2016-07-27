<?php
namespace ITECH\Admin\Form;

class MessageForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options = array()) {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tiêu đề.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);
        
        $description = new \Phalcon\Forms\Element\Textarea('description');
        $description->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập nội dung thông báo.'
            ))
        ));
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);

        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getMessageType());
        $this->add($type);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getMessageStatus());
        $this->add($status);
    }
}