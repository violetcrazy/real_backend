<?php
namespace ITECH\Api\Form\Validator;

class UserNameValidator extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (!\ITECH\Data\Lib\Util::usernameValidation($value)) {
            $validator->appendMessage(new \Phalcon\Validation\Message($message, $attribute, 'username'));

            return false;
        }
    }
}