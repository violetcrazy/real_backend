<?php
namespace ITECH\Admin\Form;

class ArticleSpecialForm extends \Phalcon\Forms\Form
{
    public function initialize($model, $options)
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
        
        $nameEng = new \Phalcon\Forms\Element\Text('name_eng');
        $nameEng->setFilters(array('striptags', 'trim'));
        $this->add($nameEng);

        $description = new \Phalcon\Forms\Element\Textarea('description');
        $description->setFilters(array('trim'));
        $this->add($description);
        
        $descriptionEng = new \Phalcon\Forms\Element\Textarea('description_eng');
        $descriptionEng->setFilters(array('trim'));
        $this->add($descriptionEng);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getArticleStatus());
        $this->add($status);
        
        $projects = \ITECH\Data\Model\ProjectModel::find(array(
            'conditions' => 'status = :status:',
            'bind' => array('status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE)
        ));
        
        $projectArray = array();
        foreach ($projects as $item) {
            $projectArray[$item->id] = $item->name;
        }
        
        $projectId = new \Phalcon\Forms\Element\Select('project_id', $projectArray);
        $this->add($projectId);
    }
}