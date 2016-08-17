<?php
namespace ITECH\Admin\Form;

class ApartmentCeriterialForm extends \Phalcon\Forms\Form
{
    public function initialize($model, $options)
    {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name);

        $name_eng = new \Phalcon\Forms\Element\Text('name_eng');
        $name_eng->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên tiếng anh.'
            ))
        ));
        $name_eng->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name_eng);

        $bathroomCount = new \Phalcon\Forms\Element\Text('bathroom_count');
        $bathroomCount->setFilters(array('int'));
        $this->add($bathroomCount);

        $bedroomCount = new \Phalcon\Forms\Element\Text('bedroom_count');
        $bedroomCount->setFilters(array('int'));
        $this->add($bedroomCount);

        $total_area = new \Phalcon\Forms\Element\Text('total_area');
        $total_area->setFilters(array('striptags', 'trim'));
        $this->add($total_area);

        $green_area = new \Phalcon\Forms\Element\Text('green_area');
        $green_area->setFilters(array('striptags', 'trim'));
        $this->add($green_area);

        $price_min = new \Phalcon\Forms\Element\Text('price_min');
        $price_min->setFilters(array('int'));
        $this->add($price_min);
        $price_min_eng = new \Phalcon\Forms\Element\Text('price_min_eng');
        $price_min_eng->setFilters(array('int'));
        $this->add($price_min_eng);

        $price_max = new \Phalcon\Forms\Element\Text('price_max');
        $price_max->setFilters(array('int'));
        $this->add($price_max);
        $price_max_eng = new \Phalcon\Forms\Element\Text('price_max_eng');
        $price_max_eng->setFilters(array('int'));
        $this->add($price_max_eng);

        $price = new \Phalcon\Forms\Element\Select('price', \ITECH\Data\Lib\Constant::getCeriterialPrice());
        $this->add($price);
        $priceEng = new \Phalcon\Forms\Element\Select('price_eng', \ITECH\Data\Lib\Constant::getCeriterialPrice());
        $this->add($priceEng);

        $direction = new \Phalcon\Forms\Element\Select('direction', \ITECH\Data\Lib\Constant::getDirection());
        $this->add($direction);

        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getCeriterialType());
        $this->add($type);

        $is_new = new \Phalcon\Forms\Element\Select('is_new', \ITECH\Data\Lib\Constant::getCeriterialIsNew());
        $this->add($is_new);

        $is_home = new \Phalcon\Forms\Element\Select('is_home', \ITECH\Data\Lib\Constant::getCeriterialIsNew());
        $this->add($is_home);

        $template = new \Phalcon\Forms\Element\Select('template', \ITECH\Data\Lib\Constant::getCeriterialTemplate());
        $this->add($template);

        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getCeriterialStatus());
        $this->add($status);

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $baseController = new \ITECH\Admin\Controller\BaseController();
        //$userSession = $this->session->get('USER');

        $data_attribute_type = $baseController->getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_TYPE);
        $attribute_type = new \Phalcon\Forms\Element\Select('attribute_type', $data_attribute_type);
        $this->add($attribute_type);

        $data_attribute_view = $baseController->getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_VIEW);
        $attribute_view = new \Phalcon\Forms\Element\Select('attribute_view', $data_attribute_view);
        $this->add($attribute_view);

        $data_attribute_utility = $baseController->getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_UTILITY);
        $attribute_utility = new \Phalcon\Forms\Element\Select('attribute_utility', $data_attribute_utility);
        $this->add($attribute_utility);

        $data_attribute_room_type = $baseController->getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_ROOM_TYPE);
        $room_type = new \Phalcon\Forms\Element\Select('room_type', $data_attribute_room_type);
        $this->add($room_type);

        $data_attribute_best_for = $baseController->getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_BEST_FOR);
        $attribute_best_for = new \Phalcon\Forms\Element\Select('best_for', $data_attribute_best_for);
        $this->add($attribute_best_for);

        $data_attribute_suitable_for = $baseController->getDataAttribute(\ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT, \ITECH\Data\Lib\Constant::APARTMENT_ATTRIBUTE_TYPE_SUITABLE_FOR);
        $attribute_suitable_for = new \Phalcon\Forms\Element\Select('attribute_suitable_for', $data_attribute_suitable_for);
        $this->add($attribute_suitable_for);

        $projectResult = \ITECH\Data\Model\ProjectModel::find(array(
            'conditions' => 'status = :project_status:',
            'bind' => array('project_status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE)
        ));
        $projects = array();
        if (count($projectResult)) {
            foreach ($projectResult as $item) {
                $projects[$item->id] = $item->name;
            }
        }

        $projectIds = new \Phalcon\Forms\Element\Select('project_ids', $projects);
        $this->add($projectIds);
    }
}
