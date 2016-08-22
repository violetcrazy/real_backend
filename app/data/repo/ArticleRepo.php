<?php
namespace ITECH\Data\Repo;

class ArticleRepo extends \ITECH\Data\Model\ArticleModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\ArticleModel::getModelsManager()->createBuilder();

        $col = array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.slug',
            'a1.slug_eng',
            'a1.intro',
            'a1.intro_eng',
            'a1.description',
            'a1.description_eng',
            'a1.gallery',
            'a1.image_default',
            'a1.view_count',
            'a1.type',
            'a1.status',
            'a1.module',
            'a1.ordering',
            'a1.created_by',
            'a1.created_ip',
            'a1.updated_by',
            'a1.created_at',
            'a1.updated_at',
            'u1.name as user_name'
        );

        if (isset($params['conditions']['module']) && $params['conditions']['module'] == \ITECH\Data\Lib\Constant::ARTICLE_MODULE_SPECIAL) {
            $colSpecial = array(
                'p1.id AS project_id',
                'p1.name AS project_name',
                'p1.name_eng AS project_name_eng',
                'p1.slug AS project_slug',
                'p1.slug_eng AS project_slug_eng',
                'p1.default_image AS project_default_image',
                'p1.address AS project_address',
                'p1.address_eng AS project_address_eng',
                'p1.province_id AS project_province_id',
                'l1.name AS project_province_name'
            );
            $col = array_merge($col, $colSpecial);

            $b->leftJoin('ITECH\Data\Model\ProjectModel', 'p1.id = a1.project_id', 'p1');
            $b->innerJoin('ITECH\Data\Model\LocationModel', 'l1.id = p1.province_id', 'l1');
        } else {
            $colCategory = array(
                'c1.id as category_id',
                'c1.name as category_name',
                'c1.name_eng as category_name_eng',
                'c1.slug as category_slug',
                'c1.slug_eng as category_slug_eng',
            );
            $col = array_merge($col, $colCategory);

            $b->innerJoin('ITECH\Data\Model\ArticleCategoryModel', 'a1.id = ac1.article_id', 'ac1');
            $b->innerJoin('ITECH\Data\Model\CategoryModel', 'c1.id = ac1.category_id', 'c1');
        }

        $b->columns($col);

        $b->from(array('a1' => 'ITECH\Data\Model\ArticleModel'));
        $b->innerJoin('ITECH\Data\Model\UserModel', 'u1.id = a1.created_by', 'u1');


        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'a1.slug LIKE :q1:';
            $query[] = 'a1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \ITECH\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['category_id'])) {
            $b->andWhere('c1.id = :category_id:', array('category_id' => $params['conditions']['category_id']));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['module'])) {
            $b->andWhere('a1.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('a1.status = :status:', array('status' => $params['conditions']['status']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('a1.updated_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));
        $page = $paginator->getPaginate();

        $dataCount = $b->getQuery()->execute()->count();
        $page->next = $page->current + 1;
        $page->before = $page->current - 1 > 0 ? $page->current - 1 : 1;
        $page->total_items = $dataCount;
        $page->total_pages = ceil($dataCount / $params['limit']);
        $page->last = $page->total_pages;

        return $page;
    }
}
