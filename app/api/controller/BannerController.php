<?php
namespace ITECH\Api\Controller;

class BannerController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function getBannerByIdAction()
    {
        $id = $this->request->getQuery('id', ['trim', 'int',  'striptags'], 0);

        $banner = \ITECH\Data\Model\BannerModel::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array(
                'id' => $id
            )
        ));

        if (!$banner) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại Banner này.',
                'result' => array()
            );
            goto RETURN_RESPONSE;
        }

        $banner->image = json_decode($banner->image);
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => $banner
        );

        RETURN_RESPONSE:
            parent::outputJSON($response);
    }
}