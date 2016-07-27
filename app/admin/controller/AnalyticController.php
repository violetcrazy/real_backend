<?php
namespace ITECH\Admin\Controller;

class AnalyticController extends \ITECH\Admin\Controller\BaseController {
    public function initialize() {
        parent::initialize();
        parent::authenticateUser();
        parent::allowRole(array(
            \ITECH\Data\Lib\Constant::USER_MEMBERSHIP_ADMIN_ADMIN
        ));
    }

    public function indexAction() {
        
        $this->view->setVars(array(

        ));
        
        $this->view->pick(parent::$theme . '/analytic/index');
    }
}