<?php
namespace ITECH\Admin\Controller;

class ImageController extends \ITECH\Admin\Controller\BaseController
{
    public function initialize()
    {
    }

    public function indexAction()
    {
        $callback = $this->request->getQuery('callback');
        $sendToElement = $this->request->getQuery('sendToElement');

        $url = $this->config->application->api_url . 'media-list';
        $responseListMedia = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $listMedia = $responseListMedia;

        $url = $this->config->application->api_url . 'folder-list';
        $responseListMedia = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $listMediaTerm = $responseListMedia;

        $category_id = $this->session->has('CATEGORY_UPLOAD') ? $this->session->get('CATEGORY_UPLOAD') : '';
        $this->view->setVars(array(
            'listMedia' => $listMedia,
            'listMediaTerm' => $listMediaTerm,
            'category_id' => $category_id,
            'callback' => $callback,
            'sendToElement' => $sendToElement
        ));
        $this->view->pick('default/image/index');
    }

    public function multipleAction()
    {
        $callback = $this->request->getQuery('callback');
        $sendToElement = $this->request->getQuery('sendToElement');

        $url = $this->config->application->api_url . 'media-list';
        $responseListMedia = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $listMedia = $responseListMedia;

        $url = $this->config->application->api_url . 'folder-list';
        $responseListMedia = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $listMediaTerm = $responseListMedia;

        $category_id = $this->session->has('CATEGORY_UPLOAD') ? $this->session->get('CATEGORY_UPLOAD') : '';
        $this->view->setVars(array(
            'listMedia' => $listMedia,
            'listMediaTerm' => $listMediaTerm,
            'category_id' => $category_id,
            'callback' => $callback,
            'sendToElement' => $sendToElement
        ));
        $this->view->pick('default/image/multiple_image');
    }

    public function managerAction()
    {
        $callback = $this->request->getQuery('callback');
        $sendToElement = $this->request->getQuery('sendToElement');

        $url = $this->config->application->api_url . 'media-list';
        $responseListMedia = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $listMedia = $responseListMedia;

        $url = $this->config->application->api_url . 'folder-list';
        $responseListMedia = json_decode(\ITECH\Data\Lib\Util::curlGet($url), true);
        $listMediaTerm = $responseListMedia;

        $category_id = $this->session->has('CATEGORY_UPLOAD') ? $this->session->get('CATEGORY_UPLOAD') : '';
        $breadcrumbs = [
            [
                'title' => 'Dashboard',
                'url' => $this->config->application->base_url,
                'active' => false
            ],
            [
                'title' => 'Quản lý hình ảnh',
                'url' => $this->url->get([
                    'for' => 'manager_image',
                ]),
                'active' => true
            ]
        ];

        $this->view->setVars(array(
            'breadcrumbs' => $breadcrumbs,
            'listMedia' => $listMedia,
            'listMediaTerm' => $listMediaTerm,
            'category_id' => $category_id,
            'callback' => $callback,
            'sendToElement' => $sendToElement
        ));
        $this->view->pick('default/image/manager');
    }
}
