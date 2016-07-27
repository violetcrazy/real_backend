<?php
namespace ITECH\Admin\Form;

class ArticleForm extends \Phalcon\Forms\Form 
{
    public function initialize($model, $options = array()) 
    {
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

        $intro = new \Phalcon\Forms\Element\Textarea('intro');
        $intro->setFilters(array('striptags', 'trim'));
        $this->add($intro);

        $description = new \Phalcon\Forms\Element\Textarea('description');
        $description->setFilters(array('trim'));
        $this->add($description);
        
        $nameEng = new \Phalcon\Forms\Element\Text('name_eng');
        $nameEng->setFilters(array('striptags', 'trim'));
        $this->add($nameEng);

        $introEng = new \Phalcon\Forms\Element\Textarea('intro_eng');
        $introEng->setFilters(array('striptags', 'trim'));
        $this->add($introEng);

        $descriptionEng = new \Phalcon\Forms\Element\Textarea('description_eng');
        $descriptionEng->setFilters(array('trim'));
        $this->add($descriptionEng);
        
        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getArticleType());
        $this->add($type);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getArticleStatus());
        $this->add($status);
    }
}