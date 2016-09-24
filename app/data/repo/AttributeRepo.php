<?php
namespace ITECH\Data\Repo;

class AttributeRepo extends \ITECH\Data\Model\AttributeModel
{

    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\AttributeModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.module',
            'a1.type',
            'a1.status',
            'a1.created_at',
            'a1.updated_at'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\AttributeModel'));

        if (isset($params['conditions']['status'])) {
            if ($params['conditions']['status'] != 'all') {
                $b->andWhere('a1.status = :block_status:', array('block_status' => $params['conditions']['status']));
            }
        } else {
            $b->andWhere('a1.status = :block_status:', array('block_status' => \ITECH\Data\Lib\Constant::BLOCK_STATUS_ACTIVE));
        }

        if (isset($params['module'])) {
            if ($params['module'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT) {
                $b->andWhere('a1.module = :module:', array('module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT));
            } else if ($params['module'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK) {
                $b->andWhere('a1.module = :module1: OR a1.module = :module2:', array(
                    'module1' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                    'module2' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK
                ));
            } else if ($params['module'] == \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT){
                $b->andWhere('a1.module = :module1: OR a1.module = :module2: OR a1.module = :module3:', array(
                    'module1' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT,
                    'module2' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
                    'module3' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT,
                ));
            }
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('a1.module DESC');
        }

        return $b->getQuery()->execute();
    }


    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\AttributeModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.module',
            'a1.type',
            'a1.status',
            'a1.created_at',
            'a1.updated_at'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\AttributeModel'));

        if (isset($params['conditions']['module'])) {
            $b->andWhere('a1.module = :module:', array('module' => $params['conditions']['module']));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['status'])) {
            $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => $params['conditions']['status']));
        } else {
            $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('a1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }

    public function getListByProject(array $params)
    {
        $b = \ITECH\Data\Model\AttributeModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.module',
            'a1.type',
            'a1.status',
            'a1.created_at',
            'a1.updated_at'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\AttributeModel'));
        $b->innerJoin('ITECH\Data\Model\ProjectAttributeModel', 'pa1.attribute_id = a1.id', 'pa1');

        $b->andWhere('a1.module = :module:', array('module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT));
        $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['project_id'])) {
            $b->andWhere('pa1.project_id = :project_id:', array('project_id' => $params['conditions']['project_id']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }

    public function getListByBlock(array $params)
    {
        $b = \ITECH\Data\Model\AttributeModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.module',
            'a1.type',
            'a1.status',
            'a1.created_at',
            'a1.updated_at'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\AttributeModel'));
        $b->innerJoin('ITECH\Data\Model\BlockAttributeModel', 'ba1.attribute_id = a1.id', 'ba1');

        $b->andWhere('a1.module = :module: OR a1.module = :module1:', array(
            'module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_BLOCK,
            'module1' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_PROJECT
        ));
        $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['block_id'])) {
            $b->andWhere('ba1.block_id = :block_id:', array('block_id' => $params['conditions']['block_id']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }

    public function getListByApartment(array $params)
    {
        $b = \ITECH\Data\Model\AttributeModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'a1.id',
            'a1.name',
            'a1.name_eng',
            'a1.module',
            'a1.type',
            'a1.status',
            'a1.created_at',
            'a1.updated_at'
        ));

        $b->from(array('a1' => 'ITECH\Data\Model\AttributeModel'));
        $b->innerJoin('ITECH\Data\Model\ApartmentAttributeModel', 'aa1.attribute_id = a1.id', 'aa1');

        $b->andWhere('a1.module = :module:', array('module' => \ITECH\Data\Lib\Constant::ATTRIBUTE_MODULE_APARTMENT));
        $b->andWhere('a1.status = :attribute_status:', array('attribute_status' => \ITECH\Data\Lib\Constant::ATTRIBUTE_STATUS_ACTIVE));

        if (isset($params['conditions']['type'])) {
            $b->andWhere('a1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['apartment_id'])) {
            $b->andWhere('aa1.apartment_id = :apartment_id:', array('apartment_id' => $params['conditions']['apartment_id']));
        }

        if (isset($params['limit'])) {
            $b->limit($params['limit']);
        }

        return $b->getQuery()->execute();
    }
}
