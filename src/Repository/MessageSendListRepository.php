<?php

namespace Hgabka\KunstmaanEmailBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MessageSendListRepository extends  EntityRepository
{
    public function deleteMessageFromAllLists(Message $message)
    {
        $this
            ->createQueryBuilder('s')
            ->delete()
            ->where('s.message = :message')
            ->setParameter('message', $message)
            ->getQuery()
            ->execute()
        ;
    }
}