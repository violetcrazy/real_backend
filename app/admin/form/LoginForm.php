<?php
namespace ITECH\Admin\Form;

class LoginForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $username = new \Phalcon\Forms\Element\Text('username');
        $username->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên đăng nhập.'
            )),
            new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên đăng nhập phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $username->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($username);

        $password = new \Phalcon\Forms\Element\Password('password');
        $password->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu.'
            ))
        ));
        $this->add($password);
    }
}