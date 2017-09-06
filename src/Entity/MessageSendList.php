<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MessageSendList
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_message_send_list")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\MessageSendListRepository")
 */
class MessageSendList extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @var MessageList
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\MessageList", inversedBy="sendLists", cascade={"persist"})
     * @ORM\JoinColumn(name="message_list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $list;

    /**
     * @var Message
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\Message", inversedBy="sendLists", cascade={"persist"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    /**
     * @return MessageList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param MessageList $list
     * @return MessageSendList
     */
    public function setList($list)
    {
        $this->list = $list;

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
     * @return MessageSendList
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

}