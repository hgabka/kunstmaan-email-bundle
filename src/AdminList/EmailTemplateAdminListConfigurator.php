<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Hgabka\KunstmaanEmailBundle\Form\EmailTemplateAdminType;
use Hgabka\KunstmaanEmailBundle\Security\EmailVoter;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * The admin list configurator for Setting.
 */
class EmailTemplateAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /** @var AuthorizationChecker */
    private $authChecker;

    /** @var string */
    private $editorRole;

    /**
     * @param EntityManager        $em          The entity manager
     * @param AuthorizationChecker $authChecker
     * @param string               $editorRole
     * @param AclHelper            $aclHelper   The acl helper
     */
    public function __construct(EntityManager $em, AuthorizationChecker $authChecker, string $editorRole, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(EmailTemplateAdminType::class);
        $this->authChecker = $authChecker;
        $this->editorRole = $editorRole;
    }

    /**
     * Configure the visible columns.
     */
    public function buildFields()
    {
        $this->addField('name', 'hgabka_kuma_email.labels.name', true);
        $this->addField('comment', 'hgabka_kuma_email.labels.comment', false);
    }

    /**
     * Build filters for admin list.
     */
    public function buildFilters()
    {
    }

    /**
     * Get bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'HgabkaKunstmaanEmailBundle';
    }

    /**
     * Get entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'EmailTemplate';
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return $this->authChecker->isGranted('ROLE_SUPER_ADMIN');
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item);
    }

    public function canExport()
    {
        return false;
    }

    /**
     * @param array|object $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return $this->authChecker->isGranted('ROLE_SUPER_ADMIN');
    }

    public function getListTitle()
    {
        return 'hgabka_kuma_email.titles.email_template.list';
    }

    /**
     * Returns edit title.
     *
     * @return null|string
     */
    public function getEditTitle()
    {
        return 'hgabka_kuma_email.titles.email_template.edit';
    }

    /**
     * Returns new title.
     *
     * @return null|string
     */
    public function getNewTitle()
    {
        return 'hgabka_kuma_email.titles.email_template.new';
    }

    public function getTabFields()
    {
        return [
            'hgabka_kuma_email.tabs.general' => ['name', 'comment', 'slug', 'isSystem'],
            'hgabka_kuma_email.tabs.content' => ['layout', 'translations'],
        ];
    }

    public function getEditTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:AdminList:EmailTemplate\edit.html.twig';
    }
}
