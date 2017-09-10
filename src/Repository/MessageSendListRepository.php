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

class MessageSendListRepository extends EntityRepository
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
