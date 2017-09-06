<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Hgabka\KunstmaanEmailBundle\Enum\MessageStatusEnum;
use Hgabka\KunstmaanExtensionBundle\Entity\TranslatableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Email layout
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_message")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\MessageRepository")
 */
class Message extends AbstractEntity implements TranslatableInterface
{
    use TranslatableTrait;
    use TimestampableEntity;

    /**
     * @Prezent\Translations(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\MessageTranslation")
     */
    private $translations;

    /**
     * @var ArrayCollection|MessageSendList[]
     *
     * @ORM\OneToMany(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\MessageSendList", cascade={"all"}, mappedBy="message", orphanRemoval=true)
     *
     * @Assert\Valid()
     */
    private $sendLists;

    /**
     * @ORM\Column(name="from_name", type="string", length=255, nullable=true)
     */
    private $fromName;

    /**
     * @ORM\Column(name="from_email", type="string", length=255, nullable=true)
     */
    private $fromEmail;

    /**
     * @ORM\Column(name="to_type", type="string", length=255, nullable=true)
     */
    private $toType;

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
     * @var \DateTime
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status = MessageStatusEnum::TYPE_INIT;

    /**
     * @var EmailLayout
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailLayout", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="email_layout_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $layout;

    /**
     * @var MessageQueue
     *
     * @ORM\OneToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\MessageQueue", inversedBy="message")
     * @ORM\JoinColumn(name="message_queue_id", referencedColumnName="id", onDelete="SET NULL")
     */

    private $queue;

    /**
     * @var integer
     * @ORM\Column(name="sent_mail", type="integer")
     */
    private $sentMail = 0;

    /**
     * @var integer
     * @ORM\Column(name="sent_success", type="integer")
     */
    private $sentSuccess = 0;

    /**
     * @var integer
     * @ORM\Column(name="sent_fail", type="integer")
     */
    private $sentFail = 0;

    /**
     * @ORM\Column(name="is_simple", type="boolean")
     */
    private $isSimple = false;

    /**
     * @var string
     * @ORM\Column(name="locale", type="string", length=2, nullable=true)
     */
    private $locale;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->sendLists = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     * @return Message
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     * @return Message
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return EmailLayout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param EmailLayout $layout
     * @return Message
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Message
     */
    public function setType($status)
    {
        if (!in_array($status, MessageStatusEnum::getAvailableStatuses())) {
            throw new \InvalidArgumentException("Invalid type");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getToType()
    {
        return $this->toType;
    }

    /**
     * @param mixed $toType
     * @return Message
     */
    public function setToType($toType)
    {
        $this->toType = $toType;

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
     * @return Message
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
     * @return Message
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
     * @return Message
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

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
     * @param mixed $sendAt
     * @return Message
     */
    public function setSendAt(\DateTime $sendAt = null)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getSentMail()
    {
        return $this->sentMail;
    }

    /**
     * @param int $sentMail
     * @return Message
     */
    public function setSentMail($sentMail)
    {
        $this->sentMail = $sentMail;

        return $this;
    }

    /**
     * @return int
     */
    public function getSentSuccess()
    {
        return $this->sentSuccess;
    }

    /**
     * @param int $sentSuccess
     * @return Message
     */
    public function setSentSuccess($sentSuccess)
    {
        $this->sentSuccess = $sentSuccess;

        return $this;
    }

    /**
     * @return int
     */
    public function getSentFail()
    {
        return $this->sentFail;
    }

    /**
     * @param int $sentFail
     * @return Message
     */
    public function setSentFail($sentFail)
    {
        $this->sentFail = $sentFail;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getisSimple()
    {
        return $this->isSimple;
    }

    /**
     * @param mixed $isSimple
     * @return Message
     */
    public function setIsSimple($isSimple)
    {
        $this->isSimple = $isSimple;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Message
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return MessageQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param MessageQueue $queue
     * @return Message
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return MessageSendList[]
     */
    public function getMessageSendLists()
    {
        return $this->sendLists;
    }

    /**
     * @param MessageSendList[] $sendLists
     *
     * @return Message
     */
    public function setSendLists($sendLists)
    {
        $this->sendLists = $sendLists;

        return $this;
    }

    /**
     * Add send list
     *
     * @param MessageSendList $sendList
     *
     * @return Message
     */
    public function addSendList(MessageSendList $sendList)
    {
        if (!$this->sendLists->contains($sendList)) {
            $this->sendLists[] = $sendList;

            $sendList->setMessage($this);
        }

        return $this;
    }

    /**
     * Remove send list
     *
     * @param MessageSendList $sendList
     */
    public function removeSendList(MessageSendList $sendList)
    {
        $this->sendLists->removeElement($sendList);
    }

    public static function getTranslationEntityClass()
    {
        return MessageTranslation::class;
    }
}