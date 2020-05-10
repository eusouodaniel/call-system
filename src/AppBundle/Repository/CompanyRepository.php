<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends EntityRepository
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
          ->from('AppBundle:Company', 'c');

      return $qb;
  }

  /**
   * Busca chamados por cliente
   * @return QueryResult
   */
  public function findBySearch($chamadoSearch)
  {
      $qb = $this->addActiveQuery();

      if($chamadoSearch->getType() != null && $chamadoSearch->getType()!=""){
        if($chamadoSearch->getType() == "PJ"){
          $qb->andWhere('c.type = :client or c.type is null')
              ->setParameter('client', $chamadoSearch->getType());
        }else{
          $qb->andWhere('c.type = :client')
              ->setParameter('client', $chamadoSearch->getType());
        }
      }

      return $qb->getQuery()->getResult();

  }

  public function findByUserId($userId) {
    $qb = $this->addActiveQuery();

    $qb->andWhere('c.user = :userId')->setParameter('userId', $userId);

    return $qb->getQuery()->getResult();
  }

  public function findOneById($id) {
    $qb = $this->addActiveQuery();
    $qb->andWhere('c.id = :companyId')->setParameter('companyId', $id);
    return $qb->getQuery()->getResult();
  }
}
