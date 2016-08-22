<?php
namespace ITECH\Api\Controller;

class AuthenController extends \ITECH\Api\Controller\BaseController
{
    public function createAuthorizedTokenAction()
    {
        $response = array();

        if (!$this->request->isPost()) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Invalid POST method.'
            );
            goto RETURN_RESPONSE;
        }

        $post = $this->request->getJsonRawBody();

        if ($post == '') {
            $post = $this->request->getPost();
        }

        $validator = new \Phalcon\Validation();

        $validator->add('application', new \Phalcon\Validation\Validator\PresenceOf(array(
            'message' => 'Yêu cầu nhập Application ID.'
        )));
        $validator->setFilters('application', array('striptags', 'trim', 'lower'));

        $validator->add('secret', new \Phalcon\Validation\Validator\PresenceOf(array(
            'message' => 'Yêu cầu nhập Application secret.'
        )));
        $validator->setFilters('secret', array('striptags', 'trim'));

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

        if ($validator->getValue('secret') != $this->config->application->secret) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Application secret không chính xác.'
            );
            goto RETURN_RESPONSE;
        }

        switch ($validator->getValue('application')) {
            default:
            case 'web':
                $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WEB;
                //cb2663ce82a9f4ba448ba435091e27bb
                break;

            case 'android':
                $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_ANDROID;
                //55f86ae6511795206fb63701dee7b4b0
                break;

            case 'ios':
                $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_IOS;
                //d1db99b589dbc9e1c5d730575e2bb816
                break;

            case 'winphone':
                $application = \ITECH\Data\Lib\Constant::USER_AUTHENTICATE_APPLICATION_WINPHONE;
                //5278eaeb0c035bd6c7d91ec24bd15af3
                break;
        }

        $token = md5($application . $this->config->application->secret);

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => $token
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}