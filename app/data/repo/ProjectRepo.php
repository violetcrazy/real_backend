<?php
namespace ITECH\Data\Repo;

class ProjectRepo extends \ITECH\Data\Model\ProjectModel
{
    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\ProjectModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'p1.id',
            'p1.name',
            'p1.name_eng',
            'p1.slug',
            'p1.slug_eng',
            'p1.description',
            'p1.description_eng',
            'p1.address',
            'p1.address_eng',
            'p1.default_image',
            'p1.image_view',
            'p1.plan_view',
            'p1.gallery',
            'p1.block_count',
            'p1.apartment_count',
            'p1.available_count',
            'p1.processing_count',
            'p1.sold_count',
            'p1.direction',
            'p1.total_area',
            'p1.green_area',
            'p1.view_count',
            'p1.status',
            'p1.created_at',
            'p1.updated_at',
            'l1.id AS province_id',
            'l1.name AS province_name',
            'l1.slug AS province_slug',
            'l2.id AS district_id',
            'l2.name AS district_name',
            'l2.slug AS district_slug'
        ));

        $b->from(array('p1' => 'ITECH\Data\Model\ProjectModel'));
        $b->innerJoin('ITECH\Data\Model\LocationModel', 'l1.id = p1.province_id', 'l1');
        $b->leftJoin('ITECH\Data\Model\LocationModel', 'l2.id = p1.district_id', 'l2');

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'p1.slug LIKE :q1:';
            $query[] = 'p1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \ITECH\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('p1.status = :project_status:', array('project_status' => $params['conditions']['status']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('p1.updated_at DESC');
        }

        return $b->getQuery()->execute();
    }

    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\ProjectModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'p1.id',
            'p1.name',
            'p1.name_eng',
            'p1.slug',
            'p1.slug_eng',
            'p1.description',
            'p1.description_eng',
            'p1.address',
            'p1.address_eng',
            'p1.default_image',
            'p1.image_view',
            'p1.plan_view',
            'p1.gallery',
            'p1.block_count',
            'p1.apartment_count',
            'p1.available_count',
            'p1.processing_count',
            'p1.sold_count',
            'p1.direction',
            'p1.total_area',
            'p1.green_area',
            'p1.view_count',
            'p1.status',
            'p1.created_at',
            'p1.updated_at',
            'l1.id AS province_id',
            'l1.name AS province_name',
            'l1.slug AS province_slug',
            'l2.id AS district_id',
            'l2.name AS district_name',
            'l2.slug AS district_slug'
        ));

        $b->from(array('p1' => 'ITECH\Data\Model\ProjectModel'));
        $b->innerJoin('ITECH\Data\Model\LocationModel', 'l1.id = p1.province_id', 'l1');
        $b->leftJoin('ITECH\Data\Model\LocationModel', 'l2.id = p1.district_id', 'l2');

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'p1.slug LIKE :q1:';
            $query[] = 'p1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \ITECH\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('p1.status = :project_status:', array('project_status' => $params['conditions']['status']));
        } else {
            $b->andWhere('p1.status = :project_status:', array('project_status' => \ITECH\Data\Lib\Constant::PROJECT_STATUS_ACTIVE));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('p1.updated_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }

    public function getDetail(array $params)
    {
        $b = \ITECH\Data\Model\ProjectModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'p1.id',
            'p1.name',
            'p1.name_eng',
            'p1.slug',
            'p1.slug_eng',
            'p1.description',
            'p1.description_eng',
            'p1.address',
            'p1.address_eng',
            'p1.address_latitude',
            'p1.address_longitude',
            'p1.default_image',
            'p1.image_view',
            'p1.plan_view',
            'p1.gallery',
            'p1.block_count',
            'p1.apartment_count',
            'p1.available_count',
            'p1.processing_count',
            'p1.sold_count',
            'p1.direction',
            'p1.total_area',
            'p1.green_area',
            'p1.view_count',
            'p1.status',
            'p1.created_by',
            'p1.updated_by',
            'p1.approved_by',
            'p1.created_at',
            'p1.updated_at',
            'l1.id As province_id',
            'l1.name AS province_name',
            'l1.slug AS province_slug',
            'l2.id As district_id',
            'l2.name AS district_name',
            'l2.slug AS district_slug'
        ));

        $b->from(array('p1' => 'ITECH\Data\Model\ProjectModel'));
        $b->innerJoin('ITECH\Data\Model\LocationModel', 'l1.id = p1.province_id', 'l1');
        $b->leftJoin('ITECH\Data\Model\LocationModel', 'l2.id = p1.district_id', 'l2');

        if (isset($params['conditions']['id'])) {
            $b->andWhere('p1.id = :id:', array('id' => $params['conditions']['id']));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('p1.status = :project_status:', array('project_status' => $params['conditions']['status']));
        }

        return $b->getQuery()->execute();
    }
}