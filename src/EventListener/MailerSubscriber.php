<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Hgabka\KunstmaanEmailBundle\Event\MailerEvent;
use Hgabka\KunstmaanEmailBundle\Logger\EmailLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailerSubscriber implements EventSubscriberInterface
{
    /** @var EmailLogger */
    protected $logger;

    /** @var string */
    protected $strategy;

    /**
     * MailerSubscriber constructor.
     *
     * @param EmailLogger $logger
     * @param string      $strategy
     */
    public function __construct(EmailLogger $logger, string $strategy)
    {
        $this->logger = $logger;
        $this->strategy = $strategy;
    }

    public static function getSubscribedEvents()
    {
        return [
            MailerEvent::EVENT_SEND_CALLED => 'onSendCalled',
            MailerEvent::EVENT_MAIL_SENT => 'onMailSent',
            MailerEvent::EVENT_ADD_HEADERS => 'onAddHeaders',
        ];
    }

    /**
     * @param MailerEvent $event
     */
    public function onSendCalled(MailerEvent $event)
    {
        if ($this->strategy === 'mailer_send') {
            $this->logger->logMessage($event);
        }
    }

    /**
     * @param MailerEvent $event
     */
    public function onMailSent(MailerEvent $event)
    {
        if ($this->strategy !== 'mailer_send') {
            $this->logger->logMessage($event);
        }
    }

    /**
     * @param MailerEvent $event
     */
    public function onAddHeaders(MailerEvent $event)
    {
        $event->setReturnValue($event->getParameter('configHeaders'));
    }
}
