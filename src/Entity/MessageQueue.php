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
     * @var string
     *
     * @ORM\Column(name="to_name", type="string", length=255, nullable=true)
     */
    private $toName;

    /**
     * @var string
     *
     * @ORM\Column(name="to_email", type="string", length=255, nullable=true)
     */
    private $toEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2, nullable=true)
     */
    private $locale;

    /**
     * @var Message
     *
     * @ORM\OneToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\Message", mappedBy="queue")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="text", nullable=true)
     */
    private $parameters;

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
