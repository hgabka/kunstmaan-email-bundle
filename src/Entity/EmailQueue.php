<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\KunstmaanExtensionBundle\Traits\TimestampableEntity;

/**
 * Email log.
 *
 * @ORM\Table(name="hg_kuma_email_email_queue")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\EmailQueueRepository")
 */
class EmailQueue extends AbstractQueue
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_from", type="text", nullable=true)
     */
    protected $from;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_to", type="text", nullable=true)
     */
    protected $to;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_cc", type="text", nullable=true)
     */
    protected $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_bcc", type="text", nullable=true)
     */
    protected $bcc;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    protected $contentText;

    /**
     * @var string
     *
     * @ORM\Column(name="content_html", type="text", nullable=true)
     */
    protected $contentHtml;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_at", type="datetime", nullable=true)
     */
    protected $sendAt;

    /**
     * @var EmailCampaign
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailCampaign", cascade={"persist"})
     * @ORM\JoinColumn(name="email_campaign_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    protected $campaign;

    /**
     * @return EmailCampaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param EmailCampaign $campaign
     *
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
        if (empty($to)) {
            return false;
        }

        $to = unserialize($to);
        if (!is_array($to)) {
            return $to === $email;
        }

        foreach ($to as $mail => $name) {
            if (!is_array($name) && $mail === $email) {
                return true;
            } elseif (is_array($name)) {
                if (in_array($email, $name, true) || array_key_exists($email, $name)) {
                    return true;
                }
            }
        }

        return false;
    }
}
