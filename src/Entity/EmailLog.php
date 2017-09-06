<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.04.
 * Time: 17:46
 */

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Email log
 *
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_email_log")
 * @ORM\Entity(repositoryClass="Hgabka\KunstmaanEmailBundle\Repository\EmailLogRepository")
 */
class EmailLog extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="string", length=255, nullable=true)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="string", length=255, nullable=true)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="string", length=255, nullable=true)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc", type="string", length=255, nullable=true)
     */
    private $bcc;

    /**
     * @var string
     *
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    private $textBody;

    /**
     * @var string
     *
     * @ORM\Column(name="content_html", type="text", nullable=true)
     */
    private $htmlBody;

    /**
     * @var string
     *
     * @ORM\Column(name="sattachment", type="string", length=255, nullable=true)
     */
    private $attachment;

    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=255, nullable=true)
     */
    private $mime;

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return EmailLog
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return EmailLog
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
     * @return EmailLog
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $cc
     * @return EmailLog
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param string $bcc
     * @return EmailLog
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextBody()
    {
        return $this->textBody;
    }

    /**
     * @param string $textBody
     * @return EmailLog
     */
    public function setTextBody($textBody)
    {
        $this->textBody = $textBody;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * @param string $htmlBody
     * @return EmailLog
     */
    public function setHtmlBody($htmlBody)
    {
        $this->htmlBody = $htmlBody;

        return $this;
    }

    /**
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     * @return EmailLog
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param string $mime
     * @return EmailLog
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }
}
