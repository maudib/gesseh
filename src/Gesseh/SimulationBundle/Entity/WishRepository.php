<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2013-2016 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\SimulationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * WishRepository
 */
class WishRepository extends EntityRepository
{
  public function getWishStudentQuery($student_id)
  {
    return $this->createQueryBuilder('w')
                ->join('w.simstudent', 't')
                ->where('t.student = :student')
                  ->setParameter('student', $student_id);
  }

  public function getWishQuery()
  {
    return $this->createQueryBuilder('w')
                ->join('w.simstudent', 't')
                ->join('w.department', 'd')
                ->join('d.hospital', 'h')
                ->join('d.accreditations', 'a')
                ->join('a.sector', 'u')
                ->where('a.end > :now')
                ->setParameter('now', new \DateTime('now'))
                ->addSelect('d')
                ->addSelect('h')
                ->addSelect('t')
                ->addSelect('a')
                ->addSelect('u')
    ;
  }

  public function getByStudent($student_id, $period_id)
  {
    $query = $this->getWishQuery();
    $query->andWhere('t.student = :student')
          ->setParameter('student', $student_id)
          ->join('d.repartitions', 'r')
          ->join('r.period', 'p')
          ->addSelect('r')
          ->andWhere('r.period = :period_id')
          ->setParameter('period_id', $period_id)
          ->addOrderBy('w.rank', 'asc');

    return $query->getQuery()->getResult();
  }

  public function findByUsername($username)
  {
    $query = $this->getWishQuery();
    $query->join('t.student', 's')
          ->join('s.user', 'v')
          ->andWhere('v.username = :username')
            ->setParameter('username', $username);

    return $query->getQuery()->getResult();
  }

  public function getStudentWishList($simstudent_id)
  {
    $query = $this->createQueryBuilder('w')
                  ->join('w.department', 'd')
                  ->join('d.accreditations', 'a')
                  ->join('a.sector', 's')
                  ->where('w.simstudent = :simstudent_id')
                  ->setParameter('simstudent_id', $simstudent_id)
                  ->andWhere('a.end > :now')
                  ->setParameter('now', new \DateTime('now'))
                  ->addSelect('d')
                  ->addSelect('a')
                  ->addSelect('s');

    return $query->getQuery()->getResult();
  }

  public function findByStudentAndRank($student_id, $rank, $period)
  {
    $query = $this->getWishStudentQuery($student_id);
    $query->join('w.department', 'd')
          ->addSelect('d')
          ->andWhere('w.rank = :rank')
          ->setParameter('rank', $rank);
    $wish = $query->getQuery()->getSingleResult();

    if ($current_repartition = $wish->getDepartment()->findRepartition($period)) {
        if($cluster_name = $current_repartition->getCluster()) {
            $query = $this->getWishQuery();
            $query->andWhere('t.student = :student_id')
                  ->setParameter('student_id', $student_id)
                  ->andWhere('r.cluster = :cluster')
                  ->setParameter('cluster', $cluster_name);

            return $query->getQuery()
                         ->getResult();
        } else {
            return array($wish);
        }
    }
  }

  public function findByStudentAndId($student_id, $id)
  {
    $query = $this->getWishStudentQuery($student_id);
    $query->andWhere('w.id = :id')
            ->setParameter('id', $id);

    return $query->getQuery()->getSingleResult();
  }

  public function findByRankAfter($student_id, $rank)
  {
    $query = $this->getWishStudentQuery($student_id);
    $query->andWhere('w.rank > :rank')
            ->setParameter('rank', $rank)
          ->addOrderBy('w.rank', 'asc');

    return $query->getQuery()->getResult();
  }

  public function getMaxRank($student_id)
  {
    $query = $this->getWishStudentQuery($student_id)
                  ->select('COUNT(w.id)');

    return $query->getQuery()->getSingleScalarResult();
  }

  public function getWishCluster($student_id, $wish_id, $period)
  {
      $query = $this->getWishQuery();
      $query->andWhere('w.id = :wish_id')
            ->setParameter('wish_id', $wish_id)
            ->andWhere('t.student = :student_id')
            ->setParameter('student_id', $student_id);
      $wish = $query->getQuery()->getSingleResult();

      if($current_repartition = $wish->getDepartment()->findRepartition($period)) {
          if($cluster_name = $current_repartition->getCluster()) {
              $query = $this->getWishQuery();
              $query->andWhere('t.student = :student_id')
                    ->setParameter('student_id', $student_id)
                    ->andWhere('r.cluster = :cluster')
                    ->setParameter('cluster', $cluster_name())
              ;

              return $query->getQuery()
                           ->getResult()
              ;
          } else {
              return array($wish);
          }
      }
  }
}
