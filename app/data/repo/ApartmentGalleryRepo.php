<?php
namespace ITECH\Data\Repo;

class ApartmentGalleryRepo extends \ITECH\Data\Model\ApartmentGalleryModel
{
    public function getPaginationList(array $params)
    {
        $b = \ITECH\Data\Model\ApartmentGalleryModel::getModelsManager()->createBuilder();

        $b->columns(array(
            'ag1.id',
            'ag1.apartment_id',
            'ag1.name',
            'ag1.price',
            'ag1.gallery'
        ));

        $b->from(array('ag1' => 'ITECH\Data\Model\ApartmentGalleryModel'));
        
        if (isset($params['conditions']['q'])) {
            $query = array();
            $query[] = 'ag1.slug LIKE :q1:';
            $query[] = 'ag1.id = :q2:';

            $b->andWhere(implode(' OR ', $query), array(
                'q1' => '%' . \MBN\Data\Lib\Util::slug($params['conditions']['q']) . '%',
                'q2' => \MBN\Data\Lib\Util::slug($params['conditions']['q']),
            ));
        }
        
        if (isset($params['conditions']['apartment_id'])) {
            $b->andWhere('ag1.apartment_id = :apartment_id:', array('apartment_id' => $params['conditions']['apartment_id']));
        }
        
        if (isset($params['order'])) {
            $b->orderBy($params['order']);
        } else {
            $b->orderBy('ag1.updated_at DESC');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            'builder' => $b,
            'page' => $params['page'],
            'limit' => $params['limit']
        ));

        return $paginator->getPaginate();
    }
}