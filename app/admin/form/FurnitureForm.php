<?php
namespace ITECH\Admin\Form;

class FurnitureForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options = array()) {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập nhà nội thất.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name);
        
        $nameEng = new \Phalcon\Forms\Element\Text('name_eng');
        $nameEng->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên tiếng anh nhà nội thất.'
            ))
        ));
        $nameEng->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($nameEng);
        
        $intro = new \Phalcon\Forms\Element\Textarea('intro');
        $intro->setFilters(array('striptags', 'trim'));
        $this->add($intro);

        $introEng = new \Phalcon\Forms\Element\Textarea('intro_eng');
        $introEng->setFilters(array('striptags', 'trim'));
        $this->add($introEng);
        
        $email = new \Phalcon\Forms\Element\Text('email');
        $email->setFilters(array('striptags', 'trim'));
        $this->add($email);
        
        $phone = new \Phalcon\Forms\Element\Text('phone');
        $phone->setFilters(array('striptags', 'trim'));
        $this->add($phone);
        
        $address = new \Phalcon\Forms\Element\Text('address');
        $address->setFilters(array('striptags', 'trim'));
        $this->add($address);
        
        $addressEng = new \Phalcon\Forms\Element\Text('address_eng');
        $addressEng->setFilters(array('striptags', 'trim'));
        $this->add($addressEng);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getFurnitureStatus());
        $this->add($status);
    }
}