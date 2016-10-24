<?php
namespace ITECH\Data\Repo;

class UserRepo extends \ITECH\Data\Model\UserModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\UserModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'u1.id',
            'u1.username',
            'u1.name',
            'u1.slug',
            'u1.gender',
            'u1.birthday',
            'u1.phone',
            'u1.address',
            'u1.province_id',
            'u1.district_id',
            'u1.email',
            'u1.avatar_image',
            'u1.cover_image',
            'u1.type',
            'u1.membership',
            'u1.is_verified',
            'u1.status',
            'u1.created_at',
            'u1.updated_at',
            'u1.logined_at'
        ));

        $b->from(array('u1' => 'ITECH\Data\Model\UserModel'));

        if (isset($params['conditions']['q'])) {
            $query   = array();
            $query[] = 'u1.username LIKE :q1:';
            $query[] = 'u1.slug LIKE :q2:';
            $query[] = 'u1.email LIKE :q3:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . $params['conditions']['q'] . '%',
                'q2' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q3' => '%' . $params['conditions']['q'] . '%'
            ));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('u1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] != '') {
            $b->andWhere('u1.status = :status:', array('status' => $params['conditions']['status']));
        }

        $b->andWhere('u1.status <> :removedStatus:', array('removedStatus' => \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED));

        if (isset($params['conditions']['is_today']) && $params['conditions']['is_today'] == true) {
            $b->andWhere('DATE_FORMAT(u1.created_at, "%Y-%m-%d") >= :created_at:', array('created_at' => date('Y-m-d')));
        }

        if (isset($params['conditions']['is_verified'])) {
            $b->andWhere('u1.is_verified = :is_verified:', array('is_verified' => $params['conditions']['is_verified']));
        }

        if (isset($params['conditions']['membership'])) {
            $b->andWhere('u1.membership = :membership:', array('membership' => $params['conditions']['membership']));
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('u1.id DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page'    => $params['page'],
            'limit'   => $params['limit']
        ));

        return $paginator->getPaginate();
    }

    public function getList(array $params)
    {
        $b = \ITECH\Data\Model\UserModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'u1.id',
            'u1.username',
            'u1.name',
            'u1.slug',
            'u1.gender',
            'u1.birthday',
            'u1.phone',
            'u1.address',
            'u1.province_id',
            'u1.district_id',
            'u1.email',
            'u1.avatar_image',
            'u1.cover_image',
            'u1.type',
            'u1.membership',
            'u1.is_verified',
            'u1.status',
            'u1.created_at',
            'u1.updated_at',
            'u1.logined_at'
        ));

        $b->from(array('u1' => 'ITECH\Data\Model\UserModel'));

        if (isset($params['conditions']['q'])) {
            $query   = array();
            $query[] = 'u1.username LIKE :q1:';
            $query[] = 'u1.slug LIKE :q2:';
            $query[] = 'u1.email LIKE :q3:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . $params['conditions']['q'] . '%',
                'q2' => '%' . \ITECH\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q3' => '%' . $params['conditions']['q'] . '%'
            ));
        }

        if (isset($params['conditions']['type'])) {
            $b->andWhere('u1.type = :type:', array('type' => $params['conditions']['type']));
        }

        if (isset($params['conditions']['status']) && $params['conditions']['status'] != '') {
            $b->andWhere('u1.status = :status:', array('status' => $params['conditions']['status']));
        }

        $b->andWhere('u1.status <> :removedStatus:', array('removedStatus' => \ITECH\Data\Lib\Constant::USER_STATUS_REMOVED));

        if (isset($params['conditions']['is_today']) && $params['conditions']['is_today'] == true) {
            $b->andWhere('DATE_FORMAT(u1.created_at, "%Y-%m-%d") >= :created_at:', array('created_at' => date('Y-m-d')));
        }

        if (isset($params['conditions']['is_verified'])) {
            $b->andWhere('u1.is_verified = :is_verified:', array('is_verified' => $params['conditions']['is_verified']));
        }

        if (isset($params['conditions']['membership'])) {
            $b->andWhere('u1.membership = :membership:', array('membership' => $params['conditions']['membership']));
        }

        if (isset($params['conditions']['createdBy']) && $params['conditions']['createdBy'] > 0) {
            $b->andWhere('u1.created_by = :createdBy:', ['createdBy' => $params['conditions']['createdBy']]);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('u1.id DESC');
        }

        return $b->getQuery()->execute();
    }

    public function getPaginationListAbc(array $params)
    {
        $b = \ITECH\Data\Model\UserModel::getModelsManager()->createBuilder();

        $b->columns(array('u1.id'));

        $b->from(array('u1' => 'ITECH\Data\Model\UserModel'));

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page'    => $params['page'],
            'limit'   => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}
