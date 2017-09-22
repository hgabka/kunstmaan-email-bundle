<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Hgabka\KunstmaanEmailBundle\Security\EmailVoter;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * The admin list configurator for Setting.
 */
class MessageSubscriberAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
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
        $this->setAdminType(new MessageSubscriberAdminType($em, $authChecker));
        $this->authChecker = $authChecker;
        $this->editorRole = $editorRole;
    }

    /**
     * Configure the visible columns.
     */
    public function buildFields()
    {
        $this->addField('name', 'hgabka_kuma_email.labels.name', true);
        $this->addField('email', 'hgabka_kuma_email.labels.email', false);
    }

    /**
     * Build filters for admin list.
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name', 't'), 'Név');
        $this->addFilter('email', new ORM\StringFilterType('name', 't'), 'Név');
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
        return 'MessageSubscriber';
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
        return 'Email sablonok';
    }

    /**
     * Returns edit title.
     *
     * @return null|string
     */
    public function getEditTitle()
    {
        return 'Email sablon szerkesztése';
    }

    /**
     * Returns new title.
     *
     * @return null|string
     */
    public function getNewTitle()
    {
        return 'Új email sablon';
    }
}
