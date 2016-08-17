<?php
namespace ITECH\Admin\Form;

class ProjectForm extends \Phalcon\Forms\Form {
    public function initialize($model, $options) {
        if ($model) {}
        if ($options) {}

        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $name = new \Phalcon\Forms\Element\Text('name');
        $name->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập tên dự án.'
            )),
            new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên dự án phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($name);

        $address = new \Phalcon\Forms\Element\Text('address');
        $address->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu nhập địa chỉ.'
            )),
            new \Phalcon\Validation\Validator\StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Địa chỉ phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $this->add($address);

        // --------- Location
        $provinces = array();
        $districts = array();
        $url = $this->config->application->api_url . 'location/list?authorized_token=' . $authorizedToken;
        $cacheName = md5(serialize(array(
            'Location',
            'Api',
            $url
        )));

        $response = $this->cache->get($cacheName);
        if (!$response) {
            $response = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName, $response);
        }

        if (isset($response['status']) && $response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && count($response['result'])) {
            $provinces = $response['result'];
            if ($model->province_id > 0) {
                foreach ($provinces as $item) {
                    if ($item['id'] == $model->province_id) {
                        $districts = $item['district'];
                        break;
                    }
                }
            }
        }

        $select_province = array();
        $select_district = array();
        if (!empty($provinces)) {
            foreach ($provinces as $item) {
                $select_province[$item['id']] = $item['name'] . ' (' . $item['project_count'] . ')';
            }
        }

        if (!empty($districts)) {
            foreach ($districts as $item) {
                $select_district[$item['id']] = $item['name'] . ' (' . $item['project_count'] . ')';
            }
        }

        $province_id = new \Phalcon\Forms\Element\Select('province_id', $select_province);
        $province_id->addValidators(array(
            new \Phalcon\Validation\Validator\PresenceOf(array(
                'message' => 'Yêu cầu chọn Tỉnh/Thành phố.'
            ))
        ));
        $this->add($province_id);

        $district_id = new \Phalcon\Forms\Element\Select('district_id', $select_district);
        $this->add($district_id);
        // --------- Location

        $description = new \Phalcon\Forms\Element\TextArea('description');
        $this->add($description);

        $direction = new \Phalcon\Forms\Element\Select('direction', \ITECH\Data\Lib\Constant::getDirection());
        $this->add($direction);

        $total_area= new \Phalcon\Forms\Element\Text('total_area');
        $total_area>setFilters(array('striptags', 'trim', 'lower'));
        $this->add($total_area);

        $green_area = new \Phalcon\Forms\Element\Text('green_area');
        $green_area->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($green_area);

        $status = new \Phalcon\Forms\Element\Select('status', \ITECH\Data\Lib\Constant::getProjectStatus());
        $this->add($status);
    }
}