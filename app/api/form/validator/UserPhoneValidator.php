<?php
namespace ITECH\Api\Form\Validator;

class UserPhoneValidator extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (!\ITECH\Data\Lib\Util::phoneValidation($value)) {
            $validator->appendMessage(new \Phalcon\Validation\Message($message, $attribute, 'phone'));
            return false;
        }

        return true;
    }
}