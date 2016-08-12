<?php
namespace ITECH\Admin\Form;

class BlockForm extends \Phalcon\Forms\Form
{
    public function initialize($model, $options)
    {
        if ($model) {}
        if ($options) {}

        $loadComponent = new \ITECH\Admin\Component\LoadComponent();
        $projects = $loadComponent->getProjectAll();
        $projects = $projects['result'];
        $options_project = array('' => 'Chọn 1 dự án');

        foreach ($projects as $project){
            $options_project[$project['id']] = $project['name'];
        }

        $permissionProjects = [];

        if (isset($options['userSession'])) {
            if ($options['userSession']['membership'] != \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_SUPERADMIN) {
                $userProjects = \ITECH\Data\Model\UserProjectModel::find([
                    'conditions' => 'userId = :userId:',
                    'bind'       => ['userId' => $options['userSession']['id']]
                ]);

                if (count($userProjects)) {
                    foreach ($userProjects as $item) {
                        $permissionProjects[] = $item->projectId;
                    }
                }
            }

            $select = [];

            if (count($options_project)) {
                foreach ($options_project as $key => $value) {
                    if (in_array($key, $permissionProjects)) {
                        $select[$key] = $value;
                    }
                }

                $options_project = $select;
            }
        }

        $project_id = new \Phalcon\Forms\Element\Select('project_id', $options_project);
        $project_id->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn dự án.'
            ))
        ));
        $project_id->setFilters(array('striptags', 'trim', 'int'));
        $this->add($project_id);

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên block.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $name_eng = new \Phalcon\Forms\Element\Text('name_eng');
        $name_eng->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên tiếng anh của Block'
            ))
        ));
        $name_eng->setFilters(array('striptags', 'trim'));
        $this->add($name_eng);

        $shortname = new \Phalcon\Forms\Element\Text('shortname');
        $shortname->setFilters(array('striptags', 'trim'));
        $this->add($shortname);

        $floor_count = new \Phalcon\Forms\Element\Select('floor_count', \ITECH\Data\Lib\Constant::floorSelect());
        $floor_count->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn số tầng.'
            ))
        ));
        $this->add($floor_count);

        $apartmentCount = new \Phalcon\Forms\Element\Text('apartment_count');
        $apartmentCount->setFilters(array('int'));
        $this->add($apartmentCount);

        $direction = new \Phalcon\Forms\Element\Select('direction', \ITECH\Data\Lib\Constant::getDirection());
        $this->add($direction);

        $total_area = new \Phalcon\Forms\Element\Text('total_area');
        $total_area->setFilters(array('striptags', 'trim'));
        $this->add($total_area);

        $green_area = new \Phalcon\Forms\Element\Text('green_area');
        $green_area->setFilters(array('striptags', 'trim'));
        $this->add($green_area);

        $description = new \Phalcon\Forms\Element\TextArea('description');
        $description->setFilters(array('striptags', 'trim'));
        $this->add($description);

        $description_eng = new \Phalcon\Forms\Element\TextArea('description_eng');
        $description_eng->setFilters(array('striptags', 'trim'));
        $this->add($description_eng);

        $price = new \Phalcon\Forms\Element\Text('price');
        $price->setFilters(array('striptags', 'trim'));
        $this->add($price);

        $price_eng = new \Phalcon\Forms\Element\Text('price_eng');
        $price_eng->setFilters(array('striptags', 'trim'));
        $this->add($price_eng);

        $policy = new \Phalcon\Forms\Element\TextArea('policy');
        $policy->setFilters(array('striptags', 'trim'));
        $this->add($policy);

        $policy_eng = new \Phalcon\Forms\Element\TextArea('policy_eng');
        $policy_eng->setFilters(array('striptags', 'trim'));
        $this->add($policy_eng);

        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getBlockStatus());
        $this->add($status);
    }
}
