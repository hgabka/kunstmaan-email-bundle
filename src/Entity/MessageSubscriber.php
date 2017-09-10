<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\KunstmaanExtensionBundle\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subscriber.
 *
 * @ORM\Table(name="hg_kuma_email_message_subscriber")
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
     *
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
     *
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
     *
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
     *
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
     * Add send list.
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
     * Remove send list.
     *
     * @param MessageListSubscription $listSubscription
     */
    public function removeListSubscription(MessageListSubscription $listSubscription)
    {
        $this->listSubscriptions->removeElement($listSubscription);
    }
}
