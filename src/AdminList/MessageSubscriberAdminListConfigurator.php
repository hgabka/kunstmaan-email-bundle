<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Hgabka\KunstmaanEmailBundle\Form\MessageSubscriberAdminType;
use Hgabka\KunstmaanEmailBundle\Helper\SubscriptionManager;
use Hgabka\KunstmaanEmailBundle\Security\EmailVoter;
use Hgabka\KunstmaanExtensionBundle\Helper\KumaUtils;
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
    public function __construct(EntityManager $em, KumaUtils $kumaUtils, SubscriptionManager $subscriptionManager, AuthorizationChecker $authChecker, string $editorRole, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new MessageSubscriberAdminType($em, $kumaUtils, $subscriptionManager, $authChecker));
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
        $this->addFilter('name', new ORM\StringFilterType('name'), 'hgabka_kuma_email.labels.name');
        $this->addFilter('email', new ORM\StringFilterType('email'), 'hgabka_kuma_email.labels.email');
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
        return $this->authChecker->isGranted($this->editorRole);
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
        return $this->authChecker->isGranted($this->editorRole);
    }

    /**
     * @param array|object $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item);
    }

    public function getListTitle()
    {
        return 'hgabka_kuma_email.titles.subscriber.list';
    }

    /**
     * Returns edit title.
     *
     * @return null|string
     */
    public function getEditTitle()
    {
        return 'hgabka_kuma_email.titles.subscriber.edit';
    }

    /**
     * Returns new title.
     *
     * @return null|string
     */
    public function getNewTitle()
    {
        return 'hgabka_kuma_email.titles.subscriber.new';
    }
}
