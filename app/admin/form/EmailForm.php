<?php
namespace ITECH\Admin\Form;

class EmailForm extends \Phalcon\Forms\Form {
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
        
        $email = new \Phalcon\Forms\Element\Text('email');
        $email->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập địa chỉ email.'
            ))
        ));
        $email->setFilters(array('striptags', 'trim'));
        $this->add($email);
        
        $description = new \Phalcon\Forms\Element\Textarea('description');
        $description->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập nội dung thông báo.'
            ))
        ));
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getMessageStatus());
        $this->add($status);
    }
}