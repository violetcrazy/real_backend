<?php
namespace ITECH\Admin\Form;

class CategoryForm extends \Phalcon\Forms\Form {
    public function initialize($model, $controller, $options = array()) {
        if ($model) {}
        if ($options) {}
        
        if ($controller) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên danh mục.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);
        
        $name_eng = new \Phalcon\Forms\Element\Text('name_eng');
        $name_eng->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập danh mục tiếng anh.'
            ))
        ));
        $name_eng->setFilters(array('striptags', 'trim'));
        $this->add($name_eng);

        $meta_title = new \Phalcon\Forms\Element\Text('meta_title');
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);
        
        $meta_title_eng = new \Phalcon\Forms\Element\Text('meta_title_eng');
        $meta_title_eng->setFilters(array('striptags', 'trim'));
        $this->add($meta_title_eng);

        $meta_keyword = new \Phalcon\Forms\Element\Textarea('meta_keyword');
        $meta_keyword->setFilters(array('striptags', 'trim'));
        $this->add($meta_keyword);
        
        $meta_keyword_eng = new \Phalcon\Forms\Element\Textarea('meta_keyword_eng');
        $meta_keyword_eng->setFilters(array('striptags', 'trim'));
        $this->add($meta_keyword_eng);

        $meta_description = new \Phalcon\Forms\Element\Textarea('meta_description');
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);
        
        $meta_description_eng = new \Phalcon\Forms\Element\Textarea('meta_description_eng');
        $meta_description_eng->setFilters(array('striptags', 'trim'));
        $this->add($meta_description_eng);
        
        $userSession = $this->session->get('USER');
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $aParams = array();
        $aParams['cache'] = false;
        $aParams['parent_id'] = (int) 0;
        
        if (isset($model->id)) {
            $aParams['not_id'] = $model->id;
        }
        
        $aParams['authorized_token'] = $authorizedToken;
        $aParams['module'] = \ITECH\Data\Lib\Constant::CATEGORY_MODULE_ARTICLE;
        $url = $this->config->application->api_url . 'home/category-list';
        $url = $url . '?' . http_build_query($aParams);

        $categorySelect = array();
        $categorySelect[0] = 'Mặc định';
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($r['status']) && $r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            foreach($r['result'] as $item) {
                $categorySelect[$item['id']] = $item['name'];
                $params = array(
                    'parent_id' => $item['id'],
                    'level' => '',
                    'sub_categories' => array()
                );
                if (isset($model->id)) {
                    $params['not_id'] = $model->id;
                }
                $subCategories = $controller->subSelect($params);
                
                if (count($subCategories)) {
                    foreach($subCategories as $k => $v) {
                        $categorySelect[$k] = $v;
                    }
                }
            }
        }
        
        $parent_id = new \Phalcon\Forms\Element\Select('parent_id', $categorySelect);
        $this->add($parent_id);
        
        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getCategoryStatus());
        $this->add($status);
    }
}