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

class EmailQueueRepository extends EntityRepository
{
    /**
     * @param string   $status
     * @param null|int $limit
     *
     * @return array
     */
    public function getQueuesByStatus($status, $limit = null)
    {
        $q = $this
            ->createQueryBuilder('q')
            ->leftJoin('q.campaign', 'c')
            ->where('q.status = :status')
            ->andWhere('c.id IS NULL OR c.isActive = 1')
            ->andWhere('q.sendAt IS NULL OR q.sendAt <= :now')
            ->orderBy('q.createdAt', 'DESC')
            ->setParameters([
                'now' => new \DateTime(),
                'status' => $status,
            ])
        ;

        if (!empty($limit)) {
            $q->setMaxResults($limit);
        }

        return $q->getQuery()->getResult();
    }

    /**
     * @param null|int $limit
     *
     * @return array
     */
    public function getErrorQueuesForSend($limit = null)
    {
        return $this->getQueuesByStatus(QueueStatusEnum::STATUS_HIBA);
    }

    /**
     * @param null|int $limit
     *
     * @return array
     */
    public function getNotSentQueuesForSend($limit = null)
    {
        return $this->getQueuesByStatus(QueueStatusEnum::STATUS_INIT);
    }
}
