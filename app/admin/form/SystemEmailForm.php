<?php
namespace ITECH\Admin\Form;

class SystemEmailForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $host = new \Phalcon\Forms\Element\Text('host');
        $host->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập host.'
            ))
        ));
        $host->setFilters(array('striptags', 'trim'));
        $this->add($host);

        $port = new \Phalcon\Forms\Element\TextArea('port');
        $port->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập port.'
            ))
        ));
        $port->setFilters(array('striptags', 'trim'));
        $this->add($port);

        $username = new \Phalcon\Forms\Element\TextArea('username');
        $username->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tài khoản email.'
            ))
        ));
        $username->setFilters(array('striptags', 'trim'));
        $this->add($username);
        
        $password = new \Phalcon\Forms\Element\Password('password');
        $password->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu.'
            ))
        ));
        $password->setFilters(array('striptags', 'trim'));
        $this->add($password);
    }
}