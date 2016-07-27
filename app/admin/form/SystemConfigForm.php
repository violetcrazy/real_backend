<?php
namespace ITECH\Admin\Form;

class SystemConfigForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $meta_title = new \Phalcon\Forms\Element\Text('meta_title');
        $meta_title->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tiêu đề.'
            ))
        ));
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);

        $meta_description = new \Phalcon\Forms\Element\TextArea('meta_description');
        $meta_description->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập mô tả.'
            ))
        ));
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);

        $meta_keywords = new \Phalcon\Forms\Element\TextArea('meta_keywords');
        $meta_keywords->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập từ khóa.'
            ))
        ));
        $meta_keywords->setFilters(array('striptags', 'trim'));
        $this->add($meta_keywords);
    }
}