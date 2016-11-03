<?php
namespace ITECH\Admin\Controller;

class HomeController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::authenticateUser();
    }

    public function indexAction()
    {
        $this->view->pick(parent::$theme . '/home/index');
    }

    public function pingAction()
    {
        echo 'pong';
    }
}