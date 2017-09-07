<?php

namespace Hgabka\KunstmaanEmailBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function getMessagesToSend()
    {
        return $this
            ->createQueryBuilder('m')
            ->where('m.sendAt IS NULL OR m.sendAt <= :date')
            ->andWhere('m.sentAt IS NULL')
            ->setParameter('date', date('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;
    }
}
