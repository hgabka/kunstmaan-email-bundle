<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminListSubscriber implements EventSubscriberInterface
{
    /** @var Registry */
    protected $doctrine;

    /**
     * MailerSubscriber constructor.
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents()
    {
        return [
            AdminListEvents::POST_ADD => 'onPostAdd',
            AdminListEvents::POST_EDIT => 'onPostEdit',
        ];
    }

    public function onPostAdd(AdminListEvent $event)
    {
        $em = $this->doctrine->getManager();

        $object = $event->getEntity();
        if ($object instanceof EmailTemplate) {
            foreach ($object->getTranslations() as $trans) {
                $attRepo = $em->getRepository('HgabkaKunstmaanEmailBundle:Attachment');
                foreach ($attRepo->getByTemplate($trans->getTranslatable(), $trans->getLocale()) as $att) {
                    $em->remove($att);
                }
                foreach ($trans->getAttachments() as $att) {
                    $att
                        ->setType(EmailTemplate::class)
                        ->setOwnerId($object->getId())
                        ->setLocale($trans->getLocale())
                    ;
                    $em->persist($att);
                }
            }

            $em->flush();
        }
    }

    public function onPostEdit(AdminListEvent $event)
    {
        $em = $this->doctrine->getManager();

        $object = $event->getEntity();
        if ($object instanceof EmailTemplate) {
            foreach ($object->getTranslations() as $trans) {
                $attRepo = $em->getRepository('HgabkaKunstmaanEmailBundle:Attachment');
                foreach ($attRepo->getByTemplate($trans->getTranslatable(), $trans->getLocale()) as $att) {
                    $em->remove($att);
                }
                foreach ($trans->getAttachments() as $att) {
                    $att
                        ->setType(EmailTemplate::class)
                        ->setOwnerId($object->getId())
                        ->setLocale($trans->getLocale())
                    ;
                    $em->persist($att);
                }
            }

            $em->flush();
        }
    }
}