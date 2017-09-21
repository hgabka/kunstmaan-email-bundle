<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Hgabka\KunstmaanEmailBundle\Entity\Message;
use Hgabka\KunstmaanEmailBundle\Form\MessageAdminType;
use Hgabka\KunstmaanEmailBundle\Security\MessageVoter;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * The admin list configurator for Message.
 */
class MessageAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /** @var AuthorizationChecker */
    private $authChecker;

    /** @var  string */
    private $editorRole;

    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AuthorizationChecker $authChecker, string $editorRole, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new MessageAdminType($em, $authChecker));
        $this->authChecker = $authChecker;
        $this->editorRole = $editorRole;
    }

    /**
     * Configure the visible columns.
     */
    public function buildFields()
    {
        $this->addField('subject', 'hgabka_kuma_email.labels.subject', true);
        $this->addField('fromEmail', 'hgabka_kuma_email.labels.from_email', false);
        $this->addField('status', 'hgabka_kuma_email.labels.status', false);
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
        return 'Message';
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canEdit($item)
    {
        return true;
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
        return true;
    }

    public function getListTitle()
    {
        return 'Körlevelek';
    }

    /**
     * Returns edit title.
     *
     * @return null|string
     */
    public function getEditTitle()
    {
        return 'Körlevél szerkesztése';
    }

    /**
     * Returns new title.
     *
     * @return null|string
     */
    public function getNewTitle()
    {
        return 'Új körlevél';
    }

    public function getTabFields()
    {
        return [
            'hgabka_kuma_email.tabs.recipients' => ['fromName', 'fromEmail'],
            'hgabka_kuma_email.tabs.content' => ['layout', 'translations'],
        ];
    }

    public function getEditTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:Message:edit.html.twig';
    }
}