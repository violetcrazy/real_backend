<?php
namespace ITECH\Admin\Form;

class ArticleFengshuiForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options = array()) {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên bài viết.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $intro = new \Phalcon\Forms\Element\Text('intro');
        $intro->setFilters(array('striptags', 'trim'));
        $this->add($intro);

        $description = new \Phalcon\Forms\Element\Textarea('description');
        $description->setFilters(array('trim'));
        $this->add($description);

        $description_eng = new \Phalcon\Forms\Element\Textarea('description_eng');
        $description_eng->setFilters(array('trim'));
        $this->add($description_eng);
        
        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getArticleFengShuiType());
        $this->add($type);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getArticleStatus());
        $this->add($status);
    }
}