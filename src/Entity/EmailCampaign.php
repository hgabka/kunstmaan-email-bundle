<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.05.
 * Time: 16:03
 */

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Hgabka\KunstmaanExtensionBundle\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Email layout
 *
 * @ORM\Table(name="hg_kuma_emailbundle_email_campaign")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\EmailCampaignRepository")
 */
class EmailCampaign extends AbstractEntity
{
    /**
     * @var MessageList
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\MessageList", inversedBy="campaigns", cascade={"persist"})
     * @ORM\JoinColumn(name="message_list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $list;

    /**
     * @var ArrayCollection|EmailCampaignMessage[]
     *
     * @ORM\OneToMany(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailCampaignMessage", cascade={"all"}, mappedBy="campaign", orphanRemoval=true)
     *
     * @Assert\Valid()
     */
    private $messages;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="from_name", type="string", length=255, nullable=true)
     */
    private $fromName;

    /**
     * @var string
     *
     * @ORM\Column(name="from_email", type="string", length=255, nullable=true)
     */
    private $fromEmail;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * @return MessageList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param MessageList $list
     * @return EmailCampaign
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EmailCampaign
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @return EmailCampaign
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
     * @return EmailCampaign
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     * @return EmailCampaign
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return EmailCampaignMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param EmailCampaignMessage[] $messages
     *
     * @return EmailCampaign
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Add message
     *
     * @param EmailCampaignMessage $message
     *
     * @return EmailCampaign
     */
    public function addMessage(EmailCampaignMessage $message)
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;

            $message->setCampaign($this);
        }

        return $this;
    }

    /**
     * Remove message
     *
     * @param EmailCampaignMessage $message
     */
    public function removeMessage(EmailCampaignMessage $message)
    {
        $this->messages->removeElement($message);
    }
}
