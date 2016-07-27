<?php
namespace ITECH\Data\Repo;

class ApartmentRequestRepo extends \ITECH\Data\Model\ApartmentRequestModel
{
    public function getPagination(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentRequestModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'ar1.id AS apartment_request_id',
            'u1.id AS user_id',
            'u1.username AS user_username',
            'u1.name AS user_name',
            'u1.phone AS user_phone',
            'u1.email AS user_email',
            'u2.id AS agent_id',
            'u2.username AS agent_username',
            'u2.name AS agent_name',
            'u2.phone AS agent_phone',
            'u2.email AS agent_email',
            'a1.id AS apartment_id',
            'a1.name AS apartment_name',
            'b1.id AS block_id',
            'b1.name AS block_name',
            'p1.id AS project_id',
            'p1.name AS project_name',
            'ar1.description AS apartment_request_description',
            'ar1.pay_method AS apartment_request_pay_method',
            'ar1.status AS apartment_request_status',
            'u3.id AS approver_id',
            'u3.name AS approver_name',
            'u3.phone AS approver_phone',
            'u3.email AS approver_email',
            'ar1.status AS apartment_request_status',
            'ar1.created_at AS apartment_request_created_at'
        ));

        $b->from(array('ar1' => 'ITECH\Data\Model\ApartmentRequestModel'));
        $b->innerJoin('ITECH\Data\Model\UserModel', 'u1.id = ar1.user_id', 'u1');
        $b->innerJoin('ITECH\Data\Model\UserModel', 'u2.id = ar1.agent_id', 'u2');
        $b->innerJoin('ITECH\Data\Model\ApartmentModel', 'a1.id = ar1.apartment_id', 'a1');
        $b->innerJoin('ITECH\Data\Model\BlockModel', 'b1.id = a1.block_id', 'b1');
        $b->innerJoin('ITECH\Data\Model\ProjectModel', 'p1.id = b1.project_id', 'p1');
        $b->leftJoin('ITECH\Data\Model\UserModel', 'u3.id = ar1.approved_by', 'u3');

        if (isset($params['conditions']['apartment_request_status'])) {
            $b->andWhere('ar1.status = :apartment_request_status:', array('apartment_request_status' => $params['conditions']['apartment_request_status']));
        }

        $page = 1;
        if (isset($params['page'])) {
            $page = abs($params['page']);
        }

        $limit = 20;
        if (isset($params['limit'])) {
            $limit = abs($params['limit']);
        }

        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('apartment_request_created_at ASC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $page,
            'limit' => $limit
        ));

        return $paginator->getPaginate();
    }
}