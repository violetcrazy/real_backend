<?php
namespace ITECH\Admin\Form\Validator;

class UserEmailValidator extends \Phalcon\Validation\Validator\Email
{
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);

        if (is_null($value) || $value == '') {
            return true;
        }

        return parent::validate($validator, $attribute);
    }
}