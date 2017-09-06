<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_email_template_translation")
 * @ORM\Entity
 */
class EmailTemplateTranslation extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @Prezent\Translatable(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate")
     */
    private $translatable;

    /**
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(name="content_text", type="text")
     */
    private $contentText = '';

    /**
     * @ORM\Column(name="content_html", type="text")
     */
    private $contentHtml = '';

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     * @return EmailTemplateTranslation
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
     * @return EmailTemplateTranslation
     */
    public function setContentText($contentText)
    {
        $this->contentHtml = $contentText;

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
     * @return EmailTemplateTranslation
     */
    public function setContentHtml($contentHtml)
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }
}