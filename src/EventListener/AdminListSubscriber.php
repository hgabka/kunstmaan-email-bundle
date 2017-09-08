<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.08.
 * Time: 20:19
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
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;

class AdminListSubscriber implements EventSubscriberInterface
{
    /** @var  Registry */
    protected $doctrine;

    /**
     * MailerSubscriber constructor.
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
                foreach($trans->getAttachments() as $att) {
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
                foreach($trans->getAttachments() as $att) {
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