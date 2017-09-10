<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
                ->setParameter('date', date('Y-m-d 00:00:00', strtotime('-'.$days.'days')));
        }

        return $q->getQuery()->execute();
    }

    public function getSendDataForMessage(Message $message)
    {
        $data = $this->createQueryBuilder('q')
                     ->select(['q.status AS status', 'COUNT(q.id) AS num'])
                     ->where('q.message = :message')
                     ->groupBy('q.status')
                     ->setParameter('message', $message)
                     ->getQuery()
                     ->getArrayResult()
        ;

        $sum = 0;
        $res = [
            QueueStatusEnum::STATUS_INIT => 0,
            QueueStatusEnum::STATUS_ELKULDVE => 0,
            QueueStatusEnum::STATUS_HIBA => 0,
            QueueStatusEnum::STATUS_SIKERTELEN => 0,
            QueueStatusEnum::STATUS_VISSZAPATTANT => 0,
        ];

        foreach ($data as $row) {
            $res[$row['status']] = $row['num'];
            $sum += $row['num'];
        }

        $res['sum'] = $sum;

        return $res;
    }
}
