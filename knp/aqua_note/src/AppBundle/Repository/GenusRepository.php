<?php
/**
 * Created by PhpStorm.
 * User: mattotoole
 * Date: 26/09/2017
 * Time: 10:31
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{

//    /**
//     * @return Genus[]
//     */
//    public function findAllPublishedOrderBySize()
//    {
//        return $this->createQueryBuilder('genus')
//            ->andWhere('genus.isPublished = :isPublished')
//            ->setParameter('isPublished', true)
//            ->orderBy('genus.speciesCount', 'DESC')
//            ->getQuery()
//            ->execute();
//    }


    public function findAllPublishedOrderedByRecentlyActive()

    {
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->leftJoin('genus.notes', 'genus_note')
            ->orderBy('genus_note.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
}