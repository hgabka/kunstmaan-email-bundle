<?php

namespace Hgabka\KunstmaanEmailBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Hgabka\KunstmaanEmailBundle\Enum\QueueStatusEnum;

class MessageQueueRepository extends EntityRepository
{
    public function deleteMessageFromQueue($message_id)
    {
        return $this
            ->createQueryBuilder('q')
            ->delete()
            ->where('q.Message = :message')
            ->setParameter('message', $message)
            ->getQuery()
            ->execute()
            ;
    }

    public function deleteEmailFromQueue($email)
    {
        return $this
            ->createQueryBuilder('q')
            ->delete()
            ->where('q.ToEmail = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->execute()
            ;
    }

    public function clearQueue($days)
    {
        $q = $this
            ->createQueryBuilder('q')
            ->delete()
            ->where('q.Status = :st', QueueStatusEnum::STATUS_ELKULDVE)
        ;

        if (!empty($days)) {
            $q
                ->andWhere('q.updated_at <= :date')
                ->setParameter('date', date('Y-m-d 00:00:00', strtotime('-' . $days . 'days')));
        }

        return $q->getQuery()->execute();
    }
}
