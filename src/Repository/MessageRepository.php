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
