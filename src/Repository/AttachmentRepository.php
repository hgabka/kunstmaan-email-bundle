<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.05.
 * Time: 19:16
 */

namespace Hgabka\KunstmaanEmailBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Hgabka\KunstmaanEmailBundle\Entity\AbstractQueue;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate;

class AttachmentRepository extends EntityRepository
{
    public function getByQueue(AbstractQueue $queue)
    {
        return $this
            ->createQueryBuilder('a')
            ->where('a.type = :type')
            ->andWhere('a.ownerId = :queueId')
            ->setParameters(
                [
                    'type'    => get_class($queue),
                    'queueId' => $queue->getId(),
                ]
            )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getByTemplate(EmailTemplate $template, $locale)
    {
        return $this
            ->createQueryBuilder('a')
            ->where('a.type = :type')
            ->andWhere('a.ownerId = :templateId')
            ->andWhere('a.locale = :locale')
            ->setParameters(
                [
                    'type'    => EmailTemplate::class,
                    'templateId' => $template->getId(),
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->getResult()
        ;
    }
}