<?php
namespace ITECH\Admin\Controller;

class ErrorController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function error404Action()
    {
        $this->response->setStatusCode(404, 'Page not found.');
        $this->view->pick(parent::$theme . '/error/error404');
    }

    public function errorAction($e)
    {
        $this->view->setVars(array(
            'message' => $e->getMessage()
        ));
        $this->view->pick(parent::$theme . '/error/error');
    }

    public function accessAction()
    {
        $this->view->setVars(array());
        $this->view->pick(parent::$theme . '/error/access');
    }
}
