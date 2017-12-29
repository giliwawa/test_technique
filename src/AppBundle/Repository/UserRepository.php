<?php
/**
 * Created by PhpStorm.
 * User: alaak
 * Date: 29/12/17
 * Time: 11:35
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function findByIds($id_array){

       $qb = $this->getEntityManager()->createQueryBuilder();
       $qb->select('u')
           ->from("AppBundle:User", "u")
           ->where($qb->expr()->in("u.id", $id_array));

       return $qb->getQuery()->getResult();
    }

}