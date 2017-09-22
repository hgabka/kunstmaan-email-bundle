<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageSendItemAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageSendItemListAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageUnprepareItemListAction;
use Hgabka\KunstmaanEmailBundle\Entity\Message;
use Hgabka\KunstmaanEmailBundle\Enum\MessageStatusEnum;
use Hgabka\KunstmaanEmailBundle\Form\MessageAdminType;
use Hgabka\KunstmaanEmailBundle\Helper\MailBuilder;
use Hgabka\KunstmaanEmailBundle\Security\EmailVoter;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /** @var  MailBuilder */
    private $mailBuilder;

    /** @var  RequestStack */
    private $requestStack;

    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AuthorizationChecker $authChecker, MailBuilder $mailBuilder, RequestStack $requestStack, string $editorRole, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new MessageAdminType($em, $mailBuilder, $authChecker));
        $this->authChecker = $authChecker;
        $this->editorRole = $editorRole;
        $this->mailBuilder = $mailBuilder;
        $this->requestStack = $requestStack;
    }

    /**
     * Configure the visible columns.
     */
    public function buildFields()
    {
        $this->addField('subject', 'hgabka_kuma_email.labels.subject', true);
        $this->addField('fromEmail', 'hgabka_kuma_email.labels.from_email', false);
        $this->addField('status', 'hgabka_kuma_email.labels.status', false, 'HgabkaKunstmaanEmailBundle:AdminList:Message\_status.html.twig');
    }

    public function buildItemActions()
    {
        $request = $this->requestStack->getCurrentRequest();
        if ('hgabkakunstmaanemailbundle_admin_message' === $request->get('_route')) {
            $this->addItemAction(new MessageSendItemListAction());
            $this->addItemAction(new MessageUnprepareItemListAction());
        } else {
            $this->addItemAction(new MessageSendItemAction());
        }
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
        return $this->authChecker->isGranted($this->editorRole);
    }

    /**
     * @return bool
     */
    public function canEdit($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item) && $item->isPrepareable();
    }

    /**
     * @return bool
     */
    public function canPrepare($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item) && $item->isPrepareable();
    }

    /**
     * @return bool
     */
    public function canUnprepare($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item) && $item->isUnprepareable();
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
        return $this->authChecker->isGranted($this->editorRole);
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
            'hgabka_kuma_email.tabs.content'    => ['layout', 'translations'],
        ];
    }

    public function getEditTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:AdminList:Message\edit.html.twig';
    }


    public function getAddTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:AdminList:Message\edit.html.twig';
    }

    public function getItemActions()
    {
        return parent::getItemActions(); // TODO: Change the autogenerated stub
    }
}