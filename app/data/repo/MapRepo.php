<?php
namespace ITECH\Data\Repo;

class MapRepo extends \ITECH\Data\Model\MapModel
{
    public function getListByProject(array $params)
    {
        $b = \ITECH\Data\Model\MapModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'm1.id',
            'm1.map_image_id',
            'm1.point',
            'b1.name as block_name',
            'b1.slug as block_slug',
            'b1.id as block_id',
            'mi1.floor as map_image_floor'
        ));

        $b->from(array('m1' => 'ITECH\Data\Model\MapModel'));
        $b->innerJoin('ITECH\Data\Model\MapImageModel', 'mi1.id = m1.map_image_id', 'mi1');
        $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = m1.item_id', 'b1');

        if (isset($params['conditions']['module'])) {
            $b->andWhere('mi1.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['conditions']['map_image_id'])) {
            $b->andWhere('m1.map_image_id = :map_image_id:', array('map_image_id' => $params['conditions']['map_image_id']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('m1.id DESC');
        }

        return $b->getQuery()->execute();
    }

    public function getListByBlock(array $params)
    {
        $b = \ITECH\Data\Model\MapModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'm1.id',
            'm1.map_image_id',
            'm1.point',
            'a1.name as apartment_name',
            'a1.id as apartment_id',
            'mi1.floor as map_image_floor',
            'mi1.type',
            'mi1.image',
        ));

        $b->from(array('m1' => 'ITECH\Data\Model\MapModel'));
        $b->innerJoin('ITECH\Data\Model\MapImageModel', 'mi1.id = m1.map_image_id', 'mi1');
        $b->innerJoin('ITECH\Data\Model\ApartmentModel', 'a1.id = m1.item_id', 'a1');

        if (isset($params['conditions']['module'])) {
            $b->andWhere('mi1.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('mi1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['item_id'])) {
            $b->andWhere('mi1.item_id = :item_id:', array('item_id' => $params['conditions']['item_id']));
        }

        if (isset($params['conditions']['map_image_id'])) {
            $b->andWhere('m1.map_image_id = :map_image_id:', array('map_image_id' => $params['conditions']['map_image_id']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('m1.id DESC');
        }

        return $b->getQuery()->execute();
    }
}
