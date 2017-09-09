<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.08.
 * Time: 19:03
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplateTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Doctrine\ManagerRegistry;

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