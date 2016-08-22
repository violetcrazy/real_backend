<?php
namespace ITECH\Api\Controller;

class CategoryController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function categoryListAction()
    {
        $module = $this->request->getQuery('module', array('int'), \ITECH\Data\Lib\Constant::CATEGORY_MODULE_ARTICLE);
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;

        $params = array(
            'conditions' => array(
                'parent_id' => 0,
                'module' => $module,
                'status' => \ITECH\Data\Lib\Constant::CATEGORY_STATUS_ACTIVE
            ),
            'page' => $page,
            'limit' => $limit
        );
        $categoryRepo = new \ITECH\Data\Repo\CategoryRepo();
        $categories = $categoryRepo->getPaginationList($params);
        $response = array();

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
        );
        
        foreach ($categories->items as $item) {
            $response['result'][] = $item;
        }
        
        parent::outputJSON($response);
    }
}