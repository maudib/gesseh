<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2015 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\RegisterBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MemberInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberInfoRepository extends EntityRepository
{
    public function getByStudentQuery($student)
    {
        return $this->getBaseQuery()
            ->where('s.id = :student')
            ->setParameter('student', $student->getId());
    }

    public function getByMembershipQuery($student, $membership)
    {
        $query = $this->getByStudentQuery($student);
        $query->andWhere('m.id = :membership')
            ->setParameter('membership', $membership->getId());

        return $query;
    }

    public function countByMembership($student, $membership)
    {
        $query = $this->getByStudentQuery($student, $membership)
            ->select('COUNT(i)');

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getBaseQuery()
    {
        return $this->createQueryBuilder('i')
            ->join('i.membership', 'm')
            ->addSelect('m')
            ->join('m.student', 's')
            ->addSelect('s')
            ->join('i.question', 'q')
            ->addSelect('q')
            ->addOrderBy('s.surname', 'asc')
            ->addOrderBy('s.name', 'asc')
            ->addOrderBy('m.payedOn', 'asc')
            ->addOrderBy('q.id', 'asc');
    }

    public function getCurrentQuery()
    {
        return $this->getBaseQuery()
            ->where('m.expiredOn > :now')
            ->setParameter('now', new \DateTime('now'));
    }

    public function getCurrentInArray()
    {
        $query = $this->getCurrentQuery();

        foreach ($query->getQuery()->getResult() as $memberinfo) {
            $array[$memberinfo->getMembership()->getId()][$memberinfo->getQuestion()->getName()] = $memberinfo->getValue();
        }

        return $array;
    }

    public function getByMembership($student, $membership)
    {
        $query = $this->getByMembershipQuery($student, $membership);

        return $query->getQuery()->getResult();
    }
}
