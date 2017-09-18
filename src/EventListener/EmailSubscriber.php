<?php

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Hgabka\KunstmaanEmailBundle\Entity\AbstractQueue;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate;

class EmailSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
  //          'postPersist',
  //          'postUpdate',
            'preRemove',
    //        'onFlush',
        ];
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
        $attachments = [];

        if ($object instanceof EmailTemplate) {
            $attachments = $manager->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByTemplate($object);
        } elseif ($object instanceof AbstractQueue) {
            $attachments = $manager->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByQueue($object);
        }

        foreach ($attachments as $attachment) {
            $manager->remove($attachment);
        }
    }
}
