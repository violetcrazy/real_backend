<?php
namespace ITECH\Admin\Form;

class ApartmentForm extends \Phalcon\Forms\Form
{
    public function initialize($model, $options)
    {
        if ($model) {}
        if ($options) {}

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên sản phẩm.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $name_eng = new \Phalcon\Forms\Element\Text('name_eng');
        $name_eng->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên tiếng anh cho sản phẩm.'
            ))
        ));
        $name_eng->setFilters(array('striptags', 'trim'));
        $this->add($name_eng);

        $rose = new \Phalcon\Forms\Element\Text('rose');
        $rose->setFilters(array('int'));
        $this->add($rose);

        $adults_count = new \Phalcon\Forms\Element\Text('adults_count');
        $adults_count->setFilters(array('int'));
        $this->add($adults_count);

        $children_count = new \Phalcon\Forms\Element\Text('children_count');
        $children_count->setFilters(array('int'));
        $this->add($children_count);

        $bedroomCount = new \Phalcon\Forms\Element\Text('bedroom_count');
        $bedroomCount->setFilters(array('int'));
        $this->add($bedroomCount);

        $bathroomCount = new \Phalcon\Forms\Element\Text('bathroom_count');
        $bathroomCount->setFilters(array('int'));
        $this->add($bathroomCount);

        // --------- Get list Agent
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $agent = array(
            '0' => 'Chọn người quản lý sản phẩm'
        );
        $url = $this->config->application->api_url . 'user/list';
        $get = array(
            'authorized_token' => $authorizedToken,
            'type' => \ITECH\Data\Lib\Constant::USER_TYPE_AGENT,
            'membership' => \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_USER_AGENT,
            'cache' => 'false'
        );

        $url = $url . '?' . http_build_query($get);
        $response = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if (isset($response['status']) && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && count($response['result'])) {
            $users = $response['result']['items'];
            foreach ($users as $item) {
                $agent[$item['id']] = $item['name'];
            }
        }
        // --------- Get list Agent

        $user_id = new \Phalcon\Forms\Element\Select('user_id', $agent);
        $this->add($user_id);

        $price = new \Phalcon\Forms\Element\Text('price');
        $price->setFilters(array('striptags', 'trim'));
        $this->add($price);

        $price_eng = new \Phalcon\Forms\Element\Text('price_eng');
        $price_eng->setFilters(array('striptags', 'trim'));
        $this->add($price_eng);

        $price_sale_off = new \Phalcon\Forms\Element\Text('price_sale_off');
        $price_sale_off->setFilters(array('striptags', 'trim'));
        $this->add($price_sale_off);

        $price_sale_off_eng = new \Phalcon\Forms\Element\Text('price_sale_off_eng');
        $price_sale_off_eng->setFilters(array('striptags', 'trim'));
        $this->add($price_sale_off_eng);

        $position = new \Phalcon\Forms\Element\Textarea('position');
        $position->setFilters(array('striptags', 'trim'));
        $this->add($position);

        $position_eng = new \Phalcon\Forms\Element\Textarea('position_eng');
        $position_eng->setFilters(array('striptags', 'trim'));
        $this->add($position_eng);

        $position_image = new \Phalcon\Forms\Element\Textarea('position_image');
        $position_image->setFilters(array('striptags', 'trim'));
        $this->add($position_image);

        $floorSelect = array();
        if (isset($model->block_floor_count) && $model->block_floor_count > 0) {
            $floorSelect = array();
            for ($i = 1; $i <= $model->block_floor_count; $i++) {
                $floorSelect[$i] = $i;
            }
        }

        $floor = new \Phalcon\Forms\Element\Select('floor', $floorSelect);
        $floor->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn tầng.'
            ))
        ));
        $this->add($floor);

        $ordering = array();
        if (isset($model->block_apartment_count) && $model->block_apartment_count > 0) {
            $ordering = array();
            for ($i = 1; $i <= $model->block_apartment_count; $i++) {
                $ordering[$i] = $i;
            }
        }

        $ordering = new \Phalcon\Forms\Element\Select('ordering', $ordering);
        $this->add($ordering);

        $condition = new \Phalcon\Forms\Element\Select('condition', \ITECH\Data\Lib\Constant::getApartmentCondition());
        $this->add($condition);

        $type = new \Phalcon\Forms\Element\Select('type', \ITECH\Data\Lib\Constant::getApartmentType());
        $this->add($type);

        $direction = new \Phalcon\Forms\Element\Select('direction', \ITECH\Data\Lib\Constant::getDirection());
        $this->add($direction);

        $totalArea = new \Phalcon\Forms\Element\Text('total_area');
        $totalArea->setFilters(array('striptags', 'trim'));
        $this->add($totalArea);

        $greenArea = new \Phalcon\Forms\Element\Text('green_area');
        $greenArea->setFilters(array('striptags', 'trim'));
        $this->add($greenArea);

        $description = new \Phalcon\Forms\Element\Textarea('description');
        $this->add($description);

        $meta_title = new \Phalcon\Forms\Element\Text('meta_title');
        $meta_title->setFilters(array('striptags', 'trim'));
        $this->add($meta_title);

        $meta_title_eng = new \Phalcon\Forms\Element\Text('meta_title_eng');
        $meta_title_eng->setFilters(array('striptags', 'trim'));
        $this->add($meta_title_eng);

        $meta_keywords = new \Phalcon\Forms\Element\Text('meta_keywords');
        $meta_keywords->setFilters(array('striptags', 'trim'));
        $this->add($meta_keywords);

        $meta_keywords_eng = new \Phalcon\Forms\Element\Text('meta_keywords_eng');
        $meta_keywords_eng->setFilters(array('striptags', 'trim'));
        $this->add($meta_keywords_eng);

        $meta_description = new \Phalcon\Forms\Element\Text('meta_description');
        $meta_description->setFilters(array('striptags', 'trim'));
        $this->add($meta_description);

        $meta_description_eng = new \Phalcon\Forms\Element\Text('meta_description_eng');
        $meta_description_eng->setFilters(array('striptags', 'trim'));
        $this->add($meta_description_eng);

        $description_eng = new \Phalcon\Forms\Element\Text('description_eng');
        $this->add($description_eng);

        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getApartmentStatus());
        $this->add($status);

        $listFurniture = \ITECH\Data\Model\FurnitureModel::find([
            "columns" => "id, name",
            'conditions' => 'status = :status:',
            'bind' => array('status' => \ITECH\Data\Lib\Constant::FURNITURE_STATUS_ACTIVE)
        ]);
        $furnitureId = new \Phalcon\Forms\Element\Select('furniture_id', $listFurniture, array(
            'using' => array('id', 'name'),
            'useEmpty' => true,
            'emptyText' => 'Chọn nhà nội thất',
            'emptyValue' => ''));
        $this->add($furnitureId);
    }
}
