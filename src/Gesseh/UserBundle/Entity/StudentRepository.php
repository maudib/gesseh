<?php

namespace Gesseh\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StudentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudentRepository extends EntityRepository
{
  public function getStudentQuery()
  {
    return $this->createQueryBuilder('s')
                ->join('s.user', 'u')
                ->join('s.grade', 'p')
                ->addSelect('u')
                ->addSelect('p');
  }

  public function getById($id)
  {
    $query = $this->getStudentQuery();
    $query->where('s.id = :id')
          ->setParameter('id', $id);

    return $query->getQuery()
                 ->getSingleResult();
  }

  public function getAll()
  {
    $query = $this->getStudentQuery();
    $query->addOrderBy('p.isActive', 'desc')
          ->addOrderBy('s.surname', 'asc')
          ->addOrderBy('s.name', 'asc')
          ->addOrderBy('p.rank', 'asc');

    return $query->getQuery();
  }

  public function getByUsername($username)
  {
    $query = $this->getStudentQuery();
    $query->where('u.username = :username')
            ->setParameter('username', $username);

    return $query->getQuery()
                 ->getSingleResult();
  }

  public function setGradeUp($current_grade, $next_grade)
  {
    $query = $this->getEntityManager()
                    ->createQuery('UPDATE GessehUserBundle:Student s SET s.grade = :next_grade WHERE s.grade = :current_grade')
                      ->setParameters(array(
                        'current_grade' => $current_grade,
                        'next_grade' => $next_grade,
                      ));

    return $query->getResult();
  }

  public function getRankingOrder()
  {
    $query = $this->getStudentQuery();
    $query->where('u.enabled = true')
          ->andWhere('p.isActive = true')
          ->addOrderBy('p.rank', 'desc')
          ->addOrderBy('s.graduate', 'asc')
          ->addOrderBy('s.ranking', 'asc');

    return $query->getQuery()->getResult();
  }
}
