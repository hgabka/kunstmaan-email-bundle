<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplateTranslation;

class EntityLoadListener
{
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $obj = $eventArgs->getEntity();
        $em = $eventArgs->getEntityManager();

        if ($obj instanceof EmailTemplateTranslation) {
            $productReflProp = $em->getClassMetadata(get_class($obj))->reflClass->getProperty('attachments');
            $productReflProp->setAccessible(true);

            $collection = new ArrayCollection();
            $attachments = $em->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByTemplate($obj->getTranslatable(), $obj->getLocale());
            foreach ($attachments as $att) {
                $collection->add($att);
            }

            $productReflProp->setValue($obj, $collection);
        }
    }
}
