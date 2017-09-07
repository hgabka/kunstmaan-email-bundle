<?php

namespace Hgabka\KunstmaanEmailBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Hgabka\KunstmaanEmailBundle\Entity\Message;
use Hgabka\KunstmaanEmailBundle\Enum\MessageStatusEnum;

class MessageRepository extends EntityRepository
{
    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function getMessagesToQueue()
    {
        return $this
            ->createQueryBuilder('n')
            ->where('n.status = :status')
            ->andWhere('n.sendAt IS NULL OR n.sendAt <= :date')
            ->setParameters(['date' => date('Y-m-d H:i:s'), 'status' => MessageStatusEnum::STATUS_KULDENDO])
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return array
     */
    public function getMessagesToUpdate()
    {
        return $this
            ->createQueryBuilder('n')
            ->where('n.status = :status')
            ->setParameter('status', MessageStatusEnum::STATUS_FOLYAMATBAN)
            ->getQuery()
            ->getResult()
            ;
    }

}
