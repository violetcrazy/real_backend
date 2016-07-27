<?php
namespace ITECH\Api\Controller;

class ErrorController extends \ITECH\Api\Controller\BaseController
{
    public function error404Action()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR_NOT_FOUND,
            'message' => 'API not found.'
        );
        return parent::outputJSON($response);
    }

    public function errorAction($e)
    {
        $response = array(
            'status' => $e->getCode(),
            'message' => $e->getMessage()
        );
        return parent::outputJSON($response);
    }
}