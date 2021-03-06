<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Entity\StatusChamado;

/**
 * ChamadoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChamadoRepository extends EntityRepository
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
          ->from('AppBundle:Chamado', 'c');

      return $qb;
  }

  /**
   * Busca de usuários do tipo user
   * @return QueryResult
   */
  public function findByUser($user)
  {
      $qb = $this->addActiveQuery();

      $qb->andWhere('c.client = :user')
          ->setParameter('user', $user);

      $query = $qb->getQuery();

      return $query->getResult();
  }

  /**
   * Busca através dos parâmetros preenchidos
   * @return QueryResult
   */
  public function findByChamadoSearch($chamadoSearch, $pending = false, $statusIdArray = null)
  {
      $qb = $this->addActiveQuery();
      $qb->leftJoin("c.client", "client");

      if($chamadoSearch->getId() != null){
        $qb->andWhere('c.id = :id')
            ->setParameter('id', $chamadoSearch->getId());
      }

      if($chamadoSearch->getClient() != null){
        $qb->andWhere('client.id = :client')
            ->setParameter('client', $chamadoSearch->getClient()->getId());
      }
      if($chamadoSearch->getCompany() !=null){
        $qb->andWhere('client.company = :company or c.enterprise = :company')
            ->setParameter('company', $chamadoSearch->getCompany());
      }

      if($chamadoSearch->getUser() != null){
        $qb->andWhere('c.responsible = :user')
            ->setParameter('user', $chamadoSearch->getUser());
      }

      if($chamadoSearch->getStatus() != null && ($chamadoSearch->getStatus()->getId() != 99)){
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

      if($pending){
        $qb->andWhere('c.dtEnd is null');
      }

      if($statusIdArray!=null){
        $qb->andWhere('c.status IN (:statusId)')
            ->setParameter('statusId', $statusIdArray);
      }

      $qb->orderBy("c.id");

      $query = $qb->getQuery();

      return $query->getResult();
  }

  /**
   * Busca de soma de pagamentos por data, cliente
   * @return QueryResult
   */
  public function findAttendanceAverageTime($chamadoSearch)
  {
      $qb = $this->addActiveQuery();

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

      $qb->andWhere('c.dtEnd is not null')
      ->orderBy("c.attendance");
      return $qb->getQuery()->getResult();

  }

  /**
  * Retorna os total de chamados a partir do filtro
  * @return QueryResult
  */
 public function countChamadosByAttendance($attendance, $chamadoSearch, $pendent=false, $concluded=false)
 {
      $qb = $this->addActiveQuery();

      $qb->select('COUNT(c.id) as totalPendentes');
      $qb->where('c.attendance = :attendance');
      $qb->setParameter('attendance', $attendance);

      if($chamadoSearch->getBeginDate() != null){
        $qb->andWhere('c.dtCreation >= :dt_creation')
        ->setParameter('dt_creation', $chamadoSearch->getBeginDate()->setTime(00, 00, 00));
      }

      if($chamadoSearch->getEndDate() != null){
        $qb->andWhere('c.dtCreation <= :dt_creation2')
        ->setParameter('dt_creation2', $chamadoSearch->getEndDate()->setTime(23, 59, 59));
      }

      if($pendent){
        $pendentStatus = array();
        $pendentStatus[] = StatusChamado::EM_ANDAMENTO;
        $pendentStatus[] = StatusChamado::AGUARDANDO;
        $pendentStatus[] = StatusChamado::AGUARDANDO_CLIENTE;
        $qb->andWhere('c.status IN (:statusId)')
            ->setParameter('statusId', $pendentStatus);
      }

      if($concluded){
        $pendentStatus = array();
        $pendentStatus[] = StatusChamado::CONCLUIDO;
        $qb->andWhere('c.status IN (:statusId)')
            ->setParameter('statusId', $pendentStatus);
      }

      $query = $qb->getQuery();

      return $query->getSingleScalarResult();
 }

  /**
   * Retorna os chamados a partir do filtro
   * @return QueryResult
   */
  public function findChamadosByAttendance($attendance, $chamadoSearch, $pendent=false, $concluded=false)
  {
       $qb = $this->addActiveQuery();

       $qb->where('c.attendance = :attendance');
       $qb->setParameter('attendance', $attendance);

       if($chamadoSearch->getBeginDate() != null){
         $qb->andWhere('c.dtCreation >= :dt_creation')
         ->setParameter('dt_creation', $chamadoSearch->getBeginDate()->setTime(00, 00, 00));
       }

       if($chamadoSearch->getEndDate() != null){
         $qb->andWhere('c.dtCreation <= :dt_creation2')
         ->setParameter('dt_creation2', $chamadoSearch->getEndDate()->setTime(23, 59, 59));
       }

       if($pendent){
         $pendentStatus = array();
         $pendentStatus[] = StatusChamado::EM_ANDAMENTO;
         $pendentStatus[] = StatusChamado::AGUARDANDO;
         $pendentStatus[] = StatusChamado::AGUARDANDO_CLIENTE;
         $qb->andWhere('c.status IN (:statusId)')
             ->setParameter('statusId', $pendentStatus);
       }

       if($concluded){
         $pendentStatus = array();
         $pendentStatus[] = StatusChamado::CONCLUIDO;
         $qb->andWhere('c.status IN (:statusId)')
             ->setParameter('statusId', $pendentStatus);
       }

       return $qb->getQuery()->getResult();
     }

     public function countByDay($chamadoSearch, $whereIn = null) {

         $em = $this->getEntityManager();
         $rsm = new ResultSetMappingBuilder($em);
         $rsm->addScalarResult('date', 'date');
         $rsm->addScalarResult('total', 'total');

         $sqlComplement = "";

         if($whereIn!=null){
          $sqlComplement = "AND chamado.attendance_id in (".$whereIn.")";
         }

         return $em->createNativeQuery("SELECT DATE(chamado.dt_creation) AS date, count(id) total ".
         "FROM chamado ".
         "WHERE chamado.dt_creation >= :begin_date ".
         "AND chamado.dt_creation <= :end_date ".
         $sqlComplement.
         "GROUP BY date", $rsm)
         ->setParameter('begin_date', $chamadoSearch->getBeginDate()->setTime(00, 00, 00))
         ->setParameter('end_date', $chamadoSearch->getEndDate()->setTime(23, 59, 59))
         ->getResult();
     }


}
