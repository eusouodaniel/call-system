<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends EntityRepository
{
    /**
     * Adiciona filtros para selecionar apenas registros ativos à query.
     * @param QueryBuilder $qb Query inicial.
     * @return QueryBuilder Query com filtros.
     */
    public function addActiveQuery(QueryBuilder $qb = null) {
        $em = $this->getEntityManager();

        if (is_null($qb)) {
            $qb = $em->createQueryBuilder();
        }

        $qb->select(array('c'))
            ->from('AppBundle:Client', 'c');

        return $qb;
    }

    /**
     * Busca chamados por cliente
     * @return QueryResult
     */
    public function findChamadoByClient($chamadoSearch)
    {
        $qb = $this->addActiveQuery();
        $qb->leftJoin('c.chamados','cha');

        if($chamadoSearch->getClient() != null){
          $qb->andWhere('c.id = :client')
              ->setParameter('client', $chamadoSearch->getClient()->getId());
        }

        if($chamadoSearch->getUser() != null){
          $qb->andWhere('cha.user = :user')
              ->setParameter('user', $chamadoSearch->getUser());
        }

        if($chamadoSearch->getStatus() != null){
          $qb->andWhere('cha.status = :status')
              ->setParameter('status', $chamadoSearch->getStatus());
        }

        if($chamadoSearch->getAttendance() != null){
          $qb->andWhere('cha.attendance = :attendance')
              ->setParameter('attendance', $chamadoSearch->getAttendance());
        }

        if($chamadoSearch->getItem() != null){
          $qb->andWhere('cha.item = :item')
              ->setParameter('item', $chamadoSearch->getItem());
        }

        if($chamadoSearch->getBeginDate() != null){
          $qb->andWhere('cha.dtCreation >= :dt_creation')
          ->setParameter('dt_creation', $chamadoSearch->getBeginDate()->setTime(00, 00, 00));
        }

        if($chamadoSearch->getEndDate() != null){
          $qb->andWhere('cha.dtCreation <= :dt_creation2')
          ->setParameter('dt_creation2', $chamadoSearch->getEndDate()->setTime(23, 59, 59));
        }

        $qb->addSelect('count(cha.id) as totalchamados')
           ->groupBy('c.id');

        $clientsArray = $qb->getQuery()->getResult();
        $result = array();

        foreach ($clientsArray as $client) {
           $client[0]->setTotalchamados($client['totalchamados']);
           array_push($result, $client[0]);
        }

        return $result;

    }
}
