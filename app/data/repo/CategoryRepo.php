<?php
namespace ITECH\Data\Repo;

class CategoryRepo extends \ITECH\Data\Model\CategoryModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\CategoryModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'c1.id',
            'c1.parent_id',
            'c1.name',
            'c1.name_eng',
            'c1.middle_name',
            'c1.slug',
            'c1.slug_eng',
            'c1.icon',
            'c1.image',
            'c1.banner',
            'c1.meta_title',
            'c1.meta_description',
            'c1.meta_keyword',
            'c1.meta_title_eng',
            'c1.meta_description_eng',
            'c1.meta_keyword_eng',
            'c1.status',
            'c1.ordering',
            'c1.article_count'
        ));

        $b->from(array('c1' => 'ITECH\Data\Model\CategoryModel'));

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'c1.slug LIKE :q1:';
            $query[] = 'c1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['parent_id'])) {
            $b->andWhere('c1.parent_id = :parent_id:', array('parent_id' => $params['conditions']['parent_id']));
        }

        if (isset($params['conditions']['no_id'])) {
            $b->andWhere('c1.id = :no_id:', array('no_id' => $params['conditions']['no_id']));
        }

        if (isset($params['conditions']['module'])) {
            $b->andWhere('c1.module = :module:', array('module' => $params['conditions']['module']));
        }
        
        if (isset($params['conditions']['status'])) {
            $b->andWhere('c1.status = :status:', array('status' => $params['conditions']['status']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('c1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }

    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\CategoryModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'c1.id',
            'c1.parent_id',
            'c1.name',
            'c1.middle_name',
            'c1.slug',
            'c1.icon',
            'c1.image',
            'c1.banner',
            'c1.meta_title',
            'c1.meta_description',
            'c1.meta_keyword',
            'c1.status',
            'c1.ordering',
            'c1.article_count'
        ));

        $b->from(array('c1' => 'ITECH\Data\Model\CategoryModel'));

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'c1.slug LIKE :q1:';
            $query[] = 'c1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['parent_id'])) {
            $b->andWhere('c1.parent_id = :parent_id:', array('parent_id' => $params['conditions']['parent_id']));
        }

        if (isset($params['conditions']['not_id'])) {
            $b->andWhere('c1.id <> :not_id:', array('not_id' => $params['conditions']['not_id']));
        }

        if (isset($params['conditions']['module'])) {
            $b->andWhere('c1.module = :module:', array('module' => $params['conditions']['module']));
        }
        
        if (isset($params['conditions']['status'])) {
            $b->andWhere('c1.status = :status:', array('status' => $params['conditions']['status']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('c1.id DESC');
        }

        return $b->getQuery()->execute();
    }
}