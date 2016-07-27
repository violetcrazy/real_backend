<?php
namespace ITECH\Admin\Form;

class BlockAttributeForm extends \Phalcon\Forms\Form 
{
    public function initialize($model, $options) 
    {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên thuộc tính.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name);

        $language = new \Phalcon\Forms\Element\Select('language', \ITECH\Data\Lib\Constant::getAttributeLanguage());
        $this->add($language);

        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getProjectAttributeType());
        $this->add($type);
    }
}