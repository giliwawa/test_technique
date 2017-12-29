<?php
/**
 * Created by PhpStorm.
 * User: alaak
 * Date: 29/12/17
 * Time: 11:57
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    public function findAllJoinedByUsers(){
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->select("g")
            ->from("AppBundle:Group", "g")
            ->join("g.users", "u");

        return $q->getQuery()->getResult();
    }
}