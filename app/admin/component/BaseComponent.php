<?php
namespace ITECH\Admin\Component;

class BaseComponent extends \Phalcon\Mvc\User\Component
{
    public function getOptions(){
        $url = $this->config->application->api_url . 'option/list?cache=true';
        $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $options = array();
        if ($r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS) {
            $options = $r['result'];
        }
        return $options;
    }
}