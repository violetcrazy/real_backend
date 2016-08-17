<?php
namespace ITECH\Api\Controller;

class ArticleController extends \ITECH\Api\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
        parent::checkAuthorizedToken();
    }

    public function listAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        //$q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $type = $this->request->getQuery('type', array('striptags', 'trim', 'int'), '');
        $status = $this->request->getQuery('status', array('striptags', 'trim', 'int'), '');
        $module = $this->request->getQuery('module', array('striptags', 'trim', 'int'), '');
        $sortField = $this->request->getQuery('sort_field', array('striptags', 'trim'), '');
        $sortBy = $this->request->getQuery('sort_by', array('striptags', 'trim', 'upper'), 'DESC');
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->request->getQuery('limit', array('int'), $this->config->application->pagination_limit);
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');

        $cid = $this->request->getQuery('category_id', array('striptags', 'trim', 'int'), '');
        $not_id = $this->request->getQuery('not_id', array('int'), '');

        $params = array(
            'conditions' => array(),
            'order' => 'a1.id DESC',
            'page' => $page,
            'limit' => $limit
        );

        if ($not_id != '') {
            $params['conditions']['not_id'] = $not_id;
        }

        if ($type != '') {
            $params['conditions']['type'] = $type;
        }

        if ($module != '') {
            $params['conditions']['module'] = $module;
        }

        if ($cid != '') {
            $params['conditions']['category_id'] = $cid;
        }

        if ($type != '') {
            $params['conditions']['type'] = $type;
        }

        if ($status != '') {
            $params['conditions']['status'] = $status;
        }

        if ($sortField != '' && $sortBy != '') {
            if (!in_array($sortBy, array('ASC', 'DESC'))) {
                $sortBy = 'DESC';
            }

            switch ($sortField) {
                case 'updated_at':
                    $params['order'] = 'a1.updated_at ' . $sortField;
                break;

                case 'id':
                    $params['order'] = 'a1.id ' . $sortBy;
                break;
            }
        }

        $cacheName = md5(serialize(array(
            'ArticleController',
            'listAction',
            'ArticleRepo',
            'getPaginationList',
            $params
        )));

        $articles = $cache == 'true' ? $this->cache->get($cacheName) : null;
        $articles = null;
        if (!$articles) {
            $articleRepo = new \ITECH\Data\Repo\ArticleRepo();
            $articles = $articleRepo->getPaginationList($params);

            if ($cache == 'true') {
                $this->cache->save($cacheName, $articles);
            }
        }

        foreach ($articles->items as $item) {
            if (is_object($item)) {
                $item = (array)$item;
            }

            $default_image_url = parent::$noImageUrl;
            $default_thumbnail_url = parent::$noImageUrl;

            if ($item['image_default'] != '') {
                $default_image_url = $item['image_default'];
                $default_thumbnail_url = $this->config->asset->frontend_url . 'upload/article/thumbnail/' . $item['image_default'];
            }

            $project = array();
            if (isset($item['project_id']) && count($item['project_id']) > 0) {
                $projectDefaultImageUrl = parent::$noImageUrl;
                if ($item['project_default_image'] != '') {
                    $projectDefaultImageUrl = $this->config->cdn->dir_upload . $item['project_default_image'];
                }

                $project = array(
                    'id' => (int)$item['project_id'],
                    'name' => $item['project_name'],
                    'name_eng' => $item['project_name_eng'],
                    'slug' => $item['project_slug'],
                    'slug_eng' => $item['project_slug_eng'],
                    'default_image' => $item['project_default_image'],
                    'default_image_url' => $projectDefaultImageUrl,
                    'province_id' => (int)$item['project_province_id'],
                    'province_name' => $item['project_province_name']
                );
            }

            $category = array();
            if (isset($item['category_id']) && count($item['category_id']) > 0) {
                $category = array(
                    'id' => (int)$item['category_id'],
                    'name' => $item['category_name'],
                    'name_eng' => $item['category_name_eng'],
                    'slug' => $item['category_slug'],
                    'slug_eng' => $item['category_slug_eng'],
                );
            }

            $response['result'][] = array(
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'name_eng' => $item['name_eng'],
                'slug' => $item['slug'],
                'slug_eng' => $item['slug_eng'],
                'intro' => $item['intro'],
                'intro_eng' => $item['intro_eng'],
                'default_image' => $item['image_default'],
                'default_image_url' => $default_image_url,
                'default_thumbnail_url' => $default_thumbnail_url,
                'gallery' => $item['gallery'],
                'module' => (int)$item['module'],
                'status' => (int)$item['status'],
                'view_count' => (int)$item['view_count'],
                'created_by' => (int)$item['created_by'],
                'updated_by' => (int)$item['updated_by'],
                'project' => $project,
                'category' => $category
            );
        }

        $response['total_items'] = $articles->total_items;
        $response['total_pages'] = isset($articles->total_pages) ? $articles->total_pages : ceil($articles->total_items / $limit);

        RETURN_RESPONSE:
            return parent::outputJSON($response);

    }

    public function detailAction()
    {
        $response = array(
            'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_SUCCESS,
            'message' => 'Success.',
            'result' => array()
        );

        $id = $this->request->getQuery('id', array('int'), '');
        $cache = $this->request->getQuery('cache', array('striptags', 'trim', 'lower'), 'true');
        $updateViewCount = $this->request->getQuery('update_view_count', array('striptags', 'trim', 'lower'), 'false');

        $cache_name = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'ArticleModel',
            'findFirst',
            $id
        )));

        $article = $cache == 'true' ? $this->cache->get($cache_name) : null;
        if (!$article) {
            $article = \ITECH\Data\Model\ArticleModel::findFirst(array(
                'conditions' => 'id = :id:',
                'bind' => array('id' => $id)
            ));

            if ($cache == 'true') {
                $this->cache->save($cache_name, $article);
            }
        }

        if (!$article) {
            $response = array(
                'status' => \ITECH\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Không tồn tại tin đăng này.'
            );
            goto RETURN_RESPONSE;
        }

        if ($updateViewCount == 'true') {
            $article->view_count = $article->view_count + 1;
            $article->save();
        }

        $default_image_url = parent::$noImageUrl;
        $default_thumbnail_url = parent::$noImageUrl;

        if ($article->image_default != '') {
            $default_image_url = $this->config->asset->frontend_url . 'upload/article/' . $article->image_default;
            $default_thumbnail_url = $this->config->asset->frontend_url . 'upload/article/thumbnail/' . $article->image_default;
        }

        // Article category ---------
        $params = array(
            'conditions' => array(
                'article_id' => (int) $id
            )
        );
        $cacheName = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'ArticleCategoryRepo',
            'getList',
            $params
        )));

        $articleCategory = $this->cache->get($cacheName);
        if (!$articleCategory) {
            $articleCategoryRepo = new \ITECH\Data\Repo\ArticleCategoryRepo();
            $articleCategory = $articleCategoryRepo->getList($params);
            $this->cache->save($cacheName, $articleCategory);
        }

        $categories = array();
        if (count($articleCategory)) {
            foreach ($articleCategory as $item) {
                $default_image_url = parent::$noImageUrl;
                $image_icon_url = parent::$noImageUrl;

                if ($item->category_image != '') {
                    $default_image_url = $this->config->asset->frontend_url . 'upload/category/' . $item->category_image;
                }

                if ($item->category_icon != '') {
                    $image_icon_url = $this->config->asset->frontend_url . 'upload/category/' . $item->category_icon;
                }

                $categories[] = array(
                    'id' => (int)$item->category_id,
                    'name' => $item->category_name,
                    'slug' => $item->category_slug,
                    'icon' => $item->category_icon,
                    'image' => $item->category_image,
                    'article_count' => $item->category_article_count,
                    'default_image_url' => $default_image_url,
                    'image_icon_url' => $image_icon_url
                );
            }
        }
        // Article Category ---------

        // User created ---------
        $cacheName = md5(serialize(array(
            'ArticleController',
            'detailAction',
            'UserModel',
            'findFirst',
            $article->created_by
        )));

        $user = $this->cache->get($cacheName);
        if (!$user) {
            $user = \ITECH\Data\Model\UserModel::findFirst(array(
                'columns' => 'name',
                'conditions' => 'id = :id:',
                'bind' => array('id' => $article->created_by)
            ));

            $this->cache->save($cacheName, $user);
        }
        // --------- User created

        $project = array();
        if ($article->project_id > 0) {
            $cacheName = md5(serialize(array(
                'ArticleController',
                'detailAction',
                'ProjectModel',
                'findFirst',
                $article->project_id
            )));

            $projectModel = $this->cache->get($cacheName);
            if (!$projectModel) {
                $projectModel = \ITECH\Data\Model\ProjectModel::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array('id' => $article->project_id)
                ));
                $this->cache->save($cacheName, $projectModel);
            }

            if ($projectModel) {
                $projectDefaultImageUrl = parent::$noImageUrl;
                if ($projectModel->default_image != '') {
                    $projectDefaultImageUrl = $this->config->cdn->dir_upload . $projectModel->default_image;
                }

                $cacheName = md5(serialize(array(
                    'ArticleController',
                    'detailAction',
                    'LocationModel',
                    'findFirst',
                    $projectModel->province_id
                )));

                $province = $this->cache->get($cacheName);
                if (!$province) {
                    $province = \ITECH\Data\Model\LocationModel::findFirst(array(
                        'conditions' => 'id = :id:',
                        'bind' => array('id' => $projectModel->province_id)
                    ));
                    $this->cache->save($cacheName, $province);
                }

                $project = array(
                    'id' => (int)$projectModel->id,
                    'name' => $projectModel->name,
                    'name_eng' => $projectModel->name_eng,
                    'slug' => $projectModel->slug,
                    'slug_eng' => $projectModel->slug_eng,
                    'default_image' => $projectModel->default_image,
                    'default_image_url' => $projectDefaultImageUrl,
                    'province_id' => isset($province->id) ? (int)$province->id : null,
                    'province_name' => isset($province->name) ? $province->name : null
                );
            }
        }

        $response['result'] = array(
            'id' => (int)$article->id,
            'name' => $article->name,
            'name_eng' => $article->name_eng,
            'slug' => $article->slug,
            'slug_eng' => $article->slug_eng,
            'intro' => $article->intro,
            'intro_eng' => $article->intro_eng,
            'description' => \ITECH\Data\Lib\Util::htmlEntityDecode($article->description),
            'description_eng' => \ITECH\Data\Lib\Util::htmlEntityDecode($article->description_eng),
            'default_image' => $article->image_default,
            'default_image_url' => $default_image_url,
            'default_thumbnail_url' => $default_thumbnail_url,
            'gallery' => $article->gallery,
            'view_count' => (int)$article->view_count,
            'module' => (int)$article->module,
            'status' => (int)$article->status,
            'created_by' => (int)$article->created_by,
            'updated_by' => (int)$article->updated_by,
            'updated_at' => (int)$article->updated_at,
            'user' => isset($user->user_name) ? $user->user_name : null,
            'categories' => $categories,
            'project' => $project
        );

        RETURN_RESPONSE:
            return parent::outputJSON($response);
    }
}