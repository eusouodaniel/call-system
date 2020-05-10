<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AttendanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttendanceRepository extends EntityRepository
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

      $qb->select(array('a'))
          ->from('AppBundle:Attendance', 'a');

      return $qb;
  }

  /**
   * Busca de soma de pagamentos por data, cliente
   * @return QueryResult
   */
  public function findAttendanceAverageTime($chamadoSearch)
  {
      $qb = $this->addActiveQuery();
      $qb->leftJoin('a.chamados','c');

      if($chamadoSearch->getUser() != null){
        $qb->andWhere('c.user = :user')
            ->setParameter('user', $chamadoSearch->getUser());
      }

      if($chamadoSearch->getClient() != null){
        $qb->andWhere('c.client = :client')
            ->setParameter('client', $chamadoSearch->getClient());
      }

      if($chamadoSearch->getStatus() != null){
        $qb->andWhere('c.status = :status')
            ->setParameter('status', $chamadoSearch->getStatus());
      }

      if($chamadoSearch->getAttendance() != null){
        $qb->andWhere('c.attendance = :attendance')
            ->setParameter('attendance', $chamadoSearch->getAttendance());
      }

      if($chamadoSearch->getItem() != null){
        $qb->andWhere('c.item = :item')
            ->setParameter('item', $chamadoSearch->getItem());
      }

      if($chamadoSearch->getBeginDate() != null){
        $qb->andWhere('c.dtCreation >= :dt_creation')
        ->setParameter('dt_creation', $chamadoSearch->getBeginDate()->setTime(00, 00, 00));
      }

      if($chamadoSearch->getEndDate() != null){
        $qb->andWhere('c.dtCreation <= :dt_creation2')
        ->setParameter('dt_creation2', $chamadoSearch->getEndDate()->setTime(23, 59, 59));
      }

      $qb->andWhere('c.dtEnd is not null');
      /*->addSelect('AVG(TIMEDIFF(c.dtUpdate, c.dtEnd)) as averagetime')
         ->groupBy('a.id');

      $chamadoArray = $qb->getQuery()->getResult();
      $result = array();

      foreach ($chamadoArray as $chamado) {
        $chamado[0]->setAveragetime($chamado['averagetime']);
        array_push($result, $chamado[0]);
      }

      return $result;*/
      return $qb->getQuery()->getResult();

  }

}