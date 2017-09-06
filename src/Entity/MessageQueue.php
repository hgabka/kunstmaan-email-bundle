<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Hgabka\KunstmaanEmailBundle\Enum\QueueStatusEnum;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * MessageQueue
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_message_queue")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\MessageQueueRepository")
 */
class MessageQueue extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @ORM\Column(name="from", type="text", nullable=true)
     */
    private $from;

    /**
     * @ORM\Column(name="to", type="text", nullable=true)
     */
    private $to;

    /**
     * @ORM\Column(name="cc", type="text", nullable=true)
     */
    private $cc;

    /**
     * @ORM\Column(name="bcc", type="text", nullable=true)
     */
    private $bcc;

    /**
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    private $contentText;

    /**
     * @ORM\Column(name="content_html", type="text", nullable=true)
     */
    private $contentHtml;

    /**
     * @ORM\Column(name="retries", type="integer")
     */
    private $retries = 0;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status = QueueStatusEnum::TYPE_INIT;

    /**
     * @var \DateTime
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @var Message
     *
     * @ORM\OneToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\Message", mappedBy="queue")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     * @return MessageQueue
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     * @return MessageQueue
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param mixed $cc
     * @return MessageQueue
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param mixed $bcc
     * @return MessageQueue
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     * @return MessageQueue
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * @param mixed $contentText
     * @return MessageQueue
     */
    public function setContentText($contentText)
    {
        $this->contentText = $contentText;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * @param mixed $contentHtml
     * @return MessageQueue
     */
    public function setContentHtml($contentHtml)
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param mixed $retries
     * @return MessageQueue
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return MessageQueue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @param \DateTime $sendAt
     * @return MessageQueue
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

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
