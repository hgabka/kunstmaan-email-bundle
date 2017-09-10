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
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * Email layout.
 *
 * @ORM\Table(name="hg_kuma_email_email_campaign_message")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\EmailCampaignMessageRepository")
 */
class EmailCampaignMessage extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @var EmailCampaign
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailCampaign", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="email_campaign_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $campaign;

    /**
     * @var EmailTemplate
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate", cascade={"persist"})
     * @ORM\JoinColumn(name="email_template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $template;

    /**
     * @var int
     *
     * @ORM\Column(name="send_after", type="integer")
     */
    protected $sendAfter = 0;

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
     * @return EmailCampaignMessage
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return EmailTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param EmailTemplate $template
     *
     * @return EmailCampaignMessage
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return int
     */
    public function getSendAfter()
    {
        return $this->sendAfter;
    }

    /**
     * @param int $sendAfter
     *
     * @return EmailCampaignMessage
     */
    public function setSendAfter($sendAfter)
    {
        $this->sendAfter = $sendAfter;

        return $this;
    }
}
