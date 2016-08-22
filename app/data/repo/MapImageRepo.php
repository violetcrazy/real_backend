<?php
namespace ITECH\Data\Repo;

class MapImageRepo extends \ITECH\Data\Model\MapImageModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\MapImageModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'mi1.id',
            'mi1.item_id',
            'mi1.type',
            'mi1.floor',
            'mi1.image',
            'mi1.module',
            'mi1.updated_by',
            'mi1.updated_at'
        ));

        $b->from(array('mi1' => 'ITECH\Data\Model\MapImageModel'));

        if (isset($params['conditions']['module'])) {
            $b->andWhere('mi1.module = :module:', array('module' => $params['conditions']['module']));
        }
        
        if (isset($params['conditions']['item_id'])) {
            $b->andWhere('mi1.item_id = :item_id:', array('item_id' => $params['conditions']['item_id']));
        }
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('mi1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
    
    public function getAll(array $params)
    {
        $b = \ITECH\Data\Model\MapImageModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'mi1.id',
            'mi1.item_id',
            'mi1.type',
            'mi1.floor',
            'mi1.image',
            'mi1.module',
            'mi1.updated_by',
            'mi1.updated_at'
        ));

        $b->from(array('mi1' => 'ITECH\Data\Model\MapImageModel'));

        if (isset($params['conditions']['module'])) {
            $b->andWhere('mi1.module = :module:', array('module' => $params['conditions']['module']));
        }
        
        if (isset($params['conditions']['item_id'])) {
            $b->andWhere('mi1.item_id = :item_id:', array('item_id' => $params['conditions']['item_id']));
        }
        
        if (isset($params['conditions']['type'])) {
            $b->andWhere('mi1.type = :type:', array('type' => $params['conditions']['type']));
        }
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        }
        return $b->getQuery()->execute();
    }
}