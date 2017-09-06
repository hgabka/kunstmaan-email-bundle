<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.04.
 * Time: 8:45
 */

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Table(name="hgabka_kunstmaanemailbundle_email_layout_translation")
 * @ORM\Entity
 */
class EmailLayoutTranslation extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @Prezent\Translatable(targetEntity="Hgabka\KunstmaanEmailBundle\Entity\EmailLayout")
     */
    private $translatable;

    /**
     * @var string
     *
     * @ORM\Column(name="content_html", type="text")
     */
    private $contentHtml = '';

    /**
     * @return string
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * @param string $contentHtml
     * @return EmailLayoutTranslation
     */
    public function setContentHtml($contentHtml)
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }
}
