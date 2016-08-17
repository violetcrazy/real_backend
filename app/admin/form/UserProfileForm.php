<?php
namespace ITECH\Admin\Form;

class UserProfileForm extends \Phalcon\Forms\Form
{
    public function initialize($model, $options)
    {
        if ($model) {}
        if ($options) {}

        $username = new \Phalcon\Forms\Element\Text('username');
        $username->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên đăng nhập.'
            )),
            new \ITECH\Admin\Form\Validator\UserUsernameValidator(array(
                'message' => 'Tên đăng nhập không hợp lệ.'
            ))
        ));
        $username->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($username);

        $new_password = new \Phalcon\Forms\Element\Text('new_password');
        $this->add($new_password);

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập họ tên.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $email = new \Phalcon\Forms\Element\Text('email');
        $email->addValidators(array(
            new \ITECH\Admin\Form\Validator\UserEmailValidator(array(
                'message' => 'Email không hợp lệ.'
            ))
        ));
        $email->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($email);
    }
}