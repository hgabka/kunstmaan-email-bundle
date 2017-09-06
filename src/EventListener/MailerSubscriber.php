<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.06.
 * Time: 20:21
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Hgabka\KunstmaanEmailBundle\Event\MailerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            MailerEvent::EVENT_SEND_CALLED => 'onSendCalled',
            MailerEvent::EVENT_MAIL_SENT => 'onMailSent'
        ];
    }

}