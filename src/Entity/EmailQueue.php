<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email log
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_email_queue")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\EmailQueueRepository")
 */
class EmailQueue extends AbstractQueue
{
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
     * @var \DateTime
     *
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @var EmailCampaign
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailCampaign", cascade={"persist"})
     * @ORM\JoinColumn(name="email_campaign_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private $campaign;

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

    public function isForEmail($email)
    {
        $to = $this->getTo();
        if (empty($to))
        {
            return false;
        }

        $to = unserialize($to);
        if (!is_array($to))
        {
            return $to == $email;
        }
        else
        {
            foreach ($to as $mail => $name)
            {
                if (!is_array($name) && $mail == $email)
                {
                    return true;
                }
                elseif(is_array($name))
                {
                    if (in_array($email, $name) || array_key_exists($email, $name))
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
