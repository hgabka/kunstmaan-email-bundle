<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageCopyItemListAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageSendItemAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageSendItemListAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageTestmailItemListAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageUnprepareItemListAction;
use Hgabka\KunstmaanEmailBundle\AdminList\ItemAction\MessageWebItemListAction;
use Hgabka\KunstmaanEmailBundle\Form\MessageAdminType;
use Hgabka\KunstmaanEmailBundle\Helper\MailBuilder;
use Hgabka\KunstmaanEmailBundle\Security\EmailVoter;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * The admin list configurator for Message.
 */
class MessageAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /** @var AuthorizationChecker */
    private $authChecker;

    /** @var string */
    private $editorRole;

    /** @var MailBuilder */
    private $mailBuilder;

    /** @var RequestStack */
    private $requestStack;

    /**
     * @param EntityManager $em The entity manager
     * @param AuthorizationChecker $authChecker
     * @param MailBuilder $mailBuilder
     * @param RequestStack $requestStack
     * @param string $editorRole
     * @param AclHelper $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AuthorizationChecker $authChecker, MailBuilder $mailBuilder, RequestStack $requestStack, string $editorRole, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(MessageAdminType::class);
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
            $this->addItemAction(new MessageTestmailItemListAction());
            $this->addItemAction(new MessageWebItemListAction());
            $this->addItemAction(new MessageCopyItemListAction());
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
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item) && $item->isPrepareable();
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canPrepare($item)
    {
        return $this->authChecker->isGranted(EmailVoter::EDIT, $item) && $item->isPrepareable();
    }

    /**
     * @param mixed $item
     *
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
        return 'hgabka_kuma_email.titles.message.list';
    }

    /**
     * Returns edit title.
     *
     * @return null|string
     */
    public function getEditTitle()
    {
        return 'hgabka_kuma_email.titles.message.edit';
    }

    /**
     * Returns new title.
     *
     * @return null|string
     */
    public function getNewTitle()
    {
        return 'hgabka_kuma_email.titles.message.new';
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
