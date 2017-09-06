<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.06.
 * Time: 20:02
 */

namespace Hgabka\KunstmaanEmailBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MailerEvent extends Event
{
    const
        EVENT_SEND_CALLED = 'email.log.mail_send_called',
        EVENT_MAIL_SENT = 'email.log.mail_sent'
    ;

    /** @var  \Swift_Message */
    private $message;

    /**
     * @return \Swift_Message
     */
    public function getMessage() : \Swift_Message
    {
        return $this->message;
    }

    /**
     * @param \Swift_Message $message
     * @return MailerEvent
     */
    public function setMessage(\Swift_Message $message) : MailerEvent
    {
        $this->message = $message;

        return $this;
    }
}
