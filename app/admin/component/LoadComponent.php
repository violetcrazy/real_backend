<?php
namespace ITECH\Admin\Component;

class LoadComponent extends \Phalcon\Mvc\User\Component
{
    public function getFileJson(array $params)
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $fileName = isset($params['conditions']['file_name']) ? $params['conditions']['file_name'] : '';
        $query = array();

        if ($fileName != '') {
            $query['file_name'] = $fileName;
        }

        $query['cache'] = 'false';
        $query['authorized_token'] = $authorizedToken;

        $content = array();
        $url = $this->config->application->api_url . 'home/file-json?' . http_build_query($query);

        $response = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        if ($response['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($response['result']) && count($response['result'])) {
            $content = $response['result'];
        }

        return $content;
    }

    public function attributeList(array $params)
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');
        $url = $this->config->application->api_url . 'home/file-json?file_name=' . $item['data'] . '&session_token=' . $sessionToken;
        $cacheName = md5(serialize(array(
            'LoadComponent',
            'attributeList',
            'Api',
            $url
        )));

        $attributes = array();
        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\MBN\Data\Lib\Util::curlGet($url), true);
        }

        if ($r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $attributes = $r['result'];
        }

        return $attributes;
    }

    public function attribute($theme, $params = array())
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $aParams = array(
            'authorized_token' => $authorizedToken
        );

        $attributeValue = array();
        if (isset($params['id']) && $params['id'] != '') {
            $aParams['id'] = $params['id'];
            $aParams['cache'] = false;
            $url = $this->config->application->api_url . 'home/apartment-value';
            $url = $url . '?' . http_build_query($aParams);
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            if ($r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
                foreach($r['result'] as $item) {
                    $attributeValue[$item['attribute_id']][] = $item['value'];
                }
            }
        }

        $url = $this->config->application->api_url . 'home/attribute-list';
        $aParams = array(
            'authorized_token' => $authorizedToken,
            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT
        );
        $url = $url . '?' . http_build_query($aParams);
        $cacheName = md5(serialize(array(
            'LoadComponent',
            'attribute',
            'Api',
            $url
        )));

        $attributes = array();
        $r = $this->cache->get($cacheName);
        if (!$r) {
            $r = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
            $this->cache->save($cacheName);
        }

        if ($r['status'] == \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS && isset($r['result']) && count($r['result'])) {
            $attributes = $r['result'];
        }

        $attributeValueList = array();
        if (count($attributes)) {
            foreach($attributes as $item) {
                $params['conditions']['file_name'] = $item['data'];
                $values = $this->getFileJson($params);
                $attributeValueList[$item['id']] = $values;
            }
        }

        $this->view->start();
        $this->view->setVars(array(
            'attributes' => $attributes,
            'attributeValueList' => $attributeValueList,
            'attributeValue' => $attributeValue
        ));
        $this->view->render($theme . '/component/load/', 'attribute');
        $this->view->finish();

        return $this->view->getContent();
    }

    public function getProjectAll()
    {
        $authorizedToken = $this->session->get('AUTHORIZED_TOKEN');

        $url = $this->config->application->api_url . 'project/all';
        $get = array('authorized_token' => $authorizedToken);
        $r   = json_decode(\ITECH\Data\Lib\Util::curlGet($url, $get), true);

        return $r;
    }
}
