<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageQueue
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_message_queue")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\MessageQueueRepository")
 */
class MessageQueue extends AbstractQueue
{
    /**
     * @var Message
     *
     * @ORM\OneToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\Message", mappedBy="queue")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessageQueue
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
