<?php
namespace ITECH\Data\Repo;

class SearchRepo extends \ITECH\Data\Model\ApartmentModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.user_id',
            'a1.block_id',
            'a1.condition',
            'a1.type',
            'a1.name',
            'a1.slug',
            'a1.price',
            'a1.price_sale_off',
            'a1.default_image',
            'a1.gallery',
            'a1.floor_count',
            'a1.view_count',
            'a1.position',
            'a1.status',
            'a1.created_by',
            'a1.updated_by',
            'a1.approved_by',
            'a1.created_at',
            'a1.updated_at',
            'b1.name as block_name',
            'p1.id as project_id',
            'p1.name as project_name',
            'p1.address as project_address'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\ApartmentModel'));
        $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
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

        if (isset($params['conditions']['block_id'])) {
            $b->andWhere('a1.block_id = :block_id:', array('block_id' => $params['conditions']['block_id']));
        }
        
        if (isset($params['conditions']['block_ids'])) {
            $b->andWhere('a1.block_id IN (:block_ids:)', array('block_ids' => $params['conditions']['block_ids']));
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

        return $paginator->getPaginate();
    }
}