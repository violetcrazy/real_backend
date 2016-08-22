<?php
namespace ITECH\Data\Repo;

class BlockRepo extends \ITECH\Data\Model\BlockModel
{
    public function checkMapLink($id)
    {
        $b = \ITECH\Data\Model\BlockModel::getModelsManager()->createBuilder();
        $b->columns(array('mp.id'));

        $b->from(array('b1' => 'ITECH\Data\Model\BlockModel'));
        $b->innerJoin('ITECH\Data\Model\MapImageModel', 'mi.item_id = b1.id' , 'mi');
        $b->innerJoin('ITECH\Data\Model\MapModel', 'mp.map_image_id = mi.id', 'mp');

        $b->andWhere('b1.id = ' . $id);
        $b->andWhere('mi.module = 2');
        $result = $b->getQuery()->execute();

        return $result;

    }

    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\BlockModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'b1.id',
            'b1.project_id',
            'b1.name',
            'b1.name_eng',
            'b1.slug',
            'b1.slug_eng',
            'b1.default_image',
            'b1.gallery',
            'b1.floor_count',
            'b1.apartment_count',
            'b1.view_count',
            'b1.status',
            'b1.created_by',
            'b1.created_at',
            'b1.updated_at',
            'p1.name AS project_name',
            'p1.name_eng AS project_name_eng'
        ));

        $b->from(array('b1' => 'ITECH\Data\Model\BlockModel'));
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

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
            $b->andWhere('b1.status = :block_status:', array('block_status' => $params['conditions']['status']));
        }

        if (isset($params['conditions']['project_id'])) {
            $b->andWhere('b1.project_id = :project_id:', array('project_id' => $params['conditions']['project_id']));
        }

        if (isset($params['conditions']['projectIdsString'])) {
            $b->andWhere('b1.project_id IN (' . $params['conditions']['projectIdsString'] . ')');
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('b1.updated_at DESC');
        }

        return $b->getQuery()->execute();
    }

    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\BlockModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'b1.id',
            'b1.project_id',
            'b1.name',
            'b1.name_eng',
            'b1.slug',
            'b1.slug_eng',
            'b1.default_image',
            'b1.gallery',
            'b1.floor_count',
            'b1.apartment_count',
            'b1.view_count',
            'b1.status',
            'b1.created_by',
            'b1.created_at',
            'b1.updated_at',
            'p1.name AS project_name',
            'p1.name_eng AS project_name_eng'
        ));

        $b->from(array('b1' => 'ITECH\Data\Model\BlockModel'));
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');

        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'p1.slug LIKE :q1:';
            $query[] = 'p1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('b1.status = :block_status:', array('block_status' => $params['conditions']['status']));
        } else {
            $b->andWhere('b1.status = :block_status:', array('block_status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE));
        }

        if (isset($params['conditions']['project_id'])) {
            $b->andWhere('b1.project_id = :project_id:', array('project_id' => $params['conditions']['project_id']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('b1.updated_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}
