<?php
namespace ITECH\Admin\Form;

class BannerForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options = array()) {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên nhóm.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name);
        
        $url = new \Phalcon\Forms\Element\Text('url');
        $url->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($url);
        
        $description = new \Phalcon\Forms\Element\Textarea('description');
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);
        $group = \ITECH\Data\Model\GroupModel::find(array(
            'conditions' => 'type = :type:',
            'bind' => array(
                'type' => \ITECH\Data\Lib\Constant::GROUP_TYPE_BANNER
            )
        ));
        
        $group_select = array();
        $group_select[''] = 'Chọn nhóm';
        if (count($group)) {
            foreach($group as $item) {
                $group_select[$item->id] = $item->name;
            }
        }
        
        $group_id = new \Phalcon\Forms\Element\Select('group_id', $group_select);
        $group_id->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn nhóm.'
            ))
        ));
        $this->add($group_id);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getGroupStatus());
        $this->add($status);
    }
}