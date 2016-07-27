<?php
namespace ITECH\Api\Controller;

class OptionController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function detailAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $key_option = $this->request->getQuery('key_option', array('striptags', 'trim', 'lower'), '');
            
        if ($key_option != '') {
            $query = array(
                'conditions' => 'key_option = :option_key_option:',
                'bind' => array('option_key_option' => $key_option)
            );
        } elseif ($id != '') {
            $query = array(
                'conditions' => 'id = :option_id:',
                'bind' => array('option_id' => $id)
            );
        }

        if ($id || $key_option) {
            $option = \ITECH\Data\Model\OptionModel::findFirst($query);

            if ($option) {
                $response['status'] = \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS;
                $response['message'] = 'Success.';
                $response['result'] = array(
                    'id' => (int)$option->id,
                    'key_option' => $option->key_option,
                    'value' => $option->value
                );
            }
            goto RETURN_RESPONSE;
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }

    public function listAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cacheName = md5(serialize(array(
            'OptionController',
            'listAction',
            'OptionModel',
            'find'
        )));

        $result = $cache == 'true' ? $this->cache->get($cacheName) : null;
        if (!$result) {
            $options = \ITECH\Data\Model\OptionModel::find();
            if (count($options)) {
                foreach ($options as $item) {
                    $result[$item->key_option] = array(
                        'id' => (int)$item->id,
                        'key_option' => $item->key_option,
                        'value' => $item->value
                    );
                }
            }

            if ($cache == 'true') {
                $this->cache->save($cacheName, $result);
            }
        }

        $response['result'] = $result;
        parent::outputJSON($response);
    }

    public function addAction()
    {
        parent::checkAuthorizedToken();

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.'
        );

        if ($this->request->isPost()) {
            $post = $this->request->getJsonRawBody();

            if ($post == '') {
                $post = $this->request->getPost();
            }

            $validator = new \Phalcon\Validation();

            $validator->setFilters('key_option', array('striptags', 'trim', 'lower'));
            $validator->setFilters('value', array('trim'));

            $messages = $validator->validate($post);
            if (count($messages)) {
                $result = array();
                foreach ($messages as $message) {
                    $result[$message->getField()] = $message->getMessage();
                }

                $response = array(
                    'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Thông tin chưa hợp lệ.',
                    'result' => $result
                );

                goto RETURN_RESPONSE;
            }

            if ($validator->getValue('key_option') && $validator->getValue('value')) {

                $value = $validator->getValue('value');
                $keyOption = $validator->getValue('key_option');

                if (in_array($keyOption, \ITECH\Data\Lib\Constant::optionNumberOnly())) {
                    $value = \ITECH\Data\Lib\Util::numberOnly($value);
                }

                $option = \ITECH\Data\Model\OptionModel::findFirst(array(
                    'conditions' => 'key_option = :option_key_option:',
                    'bind' => array('option_key_option' => $keyOption)
                ));

                if (!$option) {
                    $option = new \ITECH\Data\Model\OptionModel();
                    $option->key_option = $keyOption;
                    $option->value = $value;
                } else {
                    $option->value = $value;
                }

                try {
                    if (!$option->save()) {
                        $messages = $option->getMessages();
                        $m = isset($messages[0]) ? $messages[0]->getMessage() : 'Lỗi, không thể cập nhật.';
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => $m
                        );
                    } else {
                        $response = array(
                            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                            'message' => 'Success.',
                            'result' => array(
                                'key_option' => $option->key_option,
                                'value' => $option->value
                            )
                        );

                        $cacheName = md5(serialize(array(
                            'OptionController',
                            'listAction',
                            'OptionModel',
                            'find'
                        )));
                        $this->cache->delete($cacheName);
                    }
                } catch (\Exception $e) {
                    $response = array(
                        'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => $e->getMessage()
                    );
                }
            }
        }

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }
}
