<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.06.
 * Time: 13:57
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Hgabka\KunstmaanEmailBundle\Entity\AbstractQueue;
use Hgabka\KunstmaanEmailBundle\Entity\EmailQueue;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplateTranslation;
use Doctrine\ORM\Event\OnFlushEventArgs;

class EmailSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
  //          'postPersist',
  //          'postUpdate',
            'preRemove',
    //        'onFlush',
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $em = $args->getObjectManager();
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $em = $args->getObjectManager();

    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $manager = $args->getObjectManager();

        if (!$object instanceof AbstractQueue) {
            return;
        }

        $attachments = $manager->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByQueue($object);
        foreach ($attachments as $attachment) {
            $manager->remove($attachment);
        }
    }
}
