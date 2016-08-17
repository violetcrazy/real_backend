<?php
namespace ITECH\Api\Controller;

class MediaTermController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function getInfoMediaTermAction()
    {
        $id = $this->request->getQuery('id', array('int'), '');
        $cache_name = md5(serialize(array('MediaTermDetail_' . $id)));

        $response = json_decode($this->cache->get($cache_name), true);
        if ($response) {
            //goto END_FUNCTION;
        }
        $term = new \ITECH\Data\Model\MediaTermModel();
        $term = $term::findFirst(array(
            'conditions' => 'category_id = :category_id:',
            'bind' => array('category_id' => $id)
        ));

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'result' => array(),
            'message' => 'Không tồn tại Thư mục này.',
        );

        if ($term) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'lấy thông tin Thư mục thành công.',
                'result' => array(
                    'category_id' => $term->category_id,
                    'name' => $term->name,
                    'parent_id' => $term->parent_id,
                    'counter_media' => $term->counter_media
                )
            );

            $this->cache->save($cache_name, json_encode($response));
        }

        END_FUNCTION:
        parent::outputJSON($response);

    }

    public function getListMediaTermAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);

        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
            'message' => 'Lỗi, Chưa có Media.',
        );

        $result = array();
        $mediaTermRepo = new \ITECH\Data\Repo\MediaTermRepo();

        $terms = $mediaTermRepo->getPaginationList(array('page' => $page, 'limit' => 20));

        if ($terms && count($terms->items) > 0) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Lấy danh sách media thành công',
            );

            foreach ($terms->items as $item) {
                $result[] = array(
                    'category_id' => $item->category_id,
                    'name' => $item->name,
                    'parent_id' => $item->parent_id,
                    'counter_media' => $item->counter_media
                );
            }
            $category_id = ($this->session->has('CATEGORY_UPLOAD') ? $this->session->get('CATEGORY_UPLOAD') : 0);
            $response['category_upload_current'] = $category_id;
            $response['result'] = $result;
            $response['total_items'] = $terms->total_items;
            $response['next'] = $terms->next;
            $response['last'] = $terms->last;
            $response['before'] = $terms->before;
            $response['total_pages'] = $terms->total_pages;
        }


        parent::outputJSON($response);
    }

}
