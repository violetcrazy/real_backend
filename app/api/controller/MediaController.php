<?php
namespace ITECH\Api\Controller;

class MediaController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function getInfoMediaAction()
    {
        $id = $this->request->getQuery('id', array('int'), '');

        $media              = new \ITECH\Data\Model\MediaModel();
        $mediaDetail = $media::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'result' => array(),
            'message' => 'Không tồn tại hình này.',
        );
        if ($mediaDetail) {

            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'lấy thông tin Media thành công.',
                'result' => $this->buildItemMedia($mediaDetail)
            );
        }

        END_FUNCTION:
            parent::outputJSON($response);

    }

    public function getListMediaAction() {
        $page           = $this->request->getQuery('page',          array('int'),               '0');
        $category_id    = $this->request->getQuery('category_id',   array('int'),               '-1');
        $type           = $this->request->getQuery('type',          array('striptags', 'trim'), '');

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Lỗi, Chưa có Media.',
        );

        $result = array();
        $mediaRepo = new \ITECH\Data\Repo\MediaRepo();

        $params = array(
            'page'=> $page,
            'limit'=> 20
        );

        $params['category_id'] = $category_id;
        if ($category_id == '-1') {
            unset($params['category_id']);
        }

        if ($type) {
            //$params['type'] = $type;
        }

        $medias = $mediaRepo->getPaginationList($params);

        if ($medias && count($medias->items) > 0) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Lấy danh sách media thành công',
            );

            foreach ($medias->items as $item) {
                $result[] = $this->buildItemMedia($item);
            }

            $response['result'] = $result;
            $response['total_items'] = $medias->total_items;
            $response['next'] = $medias->next;
            $response['last'] = $medias->last;
            $response['before'] = $medias->before;
            $response['total_pages'] = $medias->total_pages;
        }


        parent::outputJSON($response);
    }

    function buildItemMedia($item)
    {
        return array(
                'id'        => $item->id,
                'name'      => $item->name,
                'url'       => $this->config->cdn->dir_upload . $item->relative_path . '/' . $item->name,
                'thumbnail' => $this->config->cdn->dir_upload . 'thumbnail/' . $item->relative_path . '/' . $item->name,
                'type'      => $item->type,
                'size'      => $item->size,
                'created_at' => $item->created_at,
                'category_id' => $item->category_id,
                'relative_path' => $item->relative_path,
                'attribute'     =>  json_decode($item->attribute)
            );
    }

}
