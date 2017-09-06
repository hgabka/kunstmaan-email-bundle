<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Hgabka\KunstmaanEmailBundle\Enum\QueueStatusEnum;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * Email log
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_email_queue")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\EmailQueueRepository")
 */
class EmailQueue extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @var EmailCampaign
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailCampaign", cascade={"persist"})
     * @ORM\JoinColumn(name="email_campaign_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private $campaign;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="text", nullable=true)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="text", nullable=true)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="text", nullable=true)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc", type="text", nullable=true)
     */
    private $bcc;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    private $contentText;

    /**
     * @var string
     *
     * @ORM\Column(name="content_html", type="text", nullable=true)
     */
    private $contentHtml;

    /**
     * @var integer
     *
     * @ORM\Column(name="retries", type="integer")
     */
    private $retries = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status = QueueStatusEnum::TYPE_INIT;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     * @return EmailQueue
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return EmailQueue
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
     * @return EmailQueue
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
     * @return EmailQueue
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
     * @return EmailQueue
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * @param string $contentText
     * @return EmailQueue
     */
    public function setContentText($contentText)
    {
        $this->contentText = $contentText;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * @param string $contentHtml
     * @return EmailQueue
     */
    public function setContentHtml($contentHtml)
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param int $retries
     * @return EmailQueue
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
     * @return EmailQueue
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
     * @return EmailQueue
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * @return EmailCampaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param EmailCampaign $campaign
     * @return EmailQueue
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }
}
