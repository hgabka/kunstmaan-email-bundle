<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.05.
 * Time: 14:09
 */

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Hgabka\KunstmaanEmailBundle\Enum\MessageStatusEnum;
use Hgabka\KunstmaanExtensionBundle\Entity\TranslatableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Subscriber
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_message_subscriber")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\MessageSubscriberRepository")
 */
class MessageSubscriber extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @var ArrayCollection|MessageListSubscription[]
     *
     * @ORM\OneToMany(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\MessageListSubscription", cascade={"all"}, mappedBy="subscriber", orphanRemoval=true)
     *
     * @Assert\Valid()
     */
    private $listSubscriptions;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="locale", type="string", length=2, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->listSubscriptions = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return MessageSubscriber
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return MessageSubscriber
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     * @return MessageSubscriber
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return MessageSubscriber
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return MessageListSubscription[]
     */
    public function getListSubscriptions()
    {
        return $this->listSubscriptions;
    }

    /**
     * @param MessageListSubscription[] $listSubscriptions
     *
     * @return MessageSubscriber
     */
    public function setListSubscriptions($listSubscriptions)
    {
        $this->listSubscriptions = $listSubscriptions;

        return $this;
    }

    /**
     * Add send list
     *
     * @param MessageListSubscription $listSubscription
     *
     * @return MessageSubscriber
     */
    public function addListSubscription(MessageListSubscription $listSubscription)
    {
        if (!$this->listSubscriptions->contains($listSubscription)) {
            $this->listSubscriptions[] = $listSubscription;

            $listSubscription->setList($this);
        }

        return $this;
    }

    /**
     * Remove send list
     *
     * @param MessageListSubscription $listSubscription
     */
    public function removeListSubscription(MessageListSubscription $listSubscription)
    {
        $this->listSubscriptions->removeElement($listSubscription);
    }
}
