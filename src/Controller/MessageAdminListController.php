<?php

namespace Hgabka\KunstmaanEmailBundle\Controller;

use Hgabka\KunstmaanEmailBundle\AdminList\MessageAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * The admin list controller for Setting.
 */
class MessageAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new MessageAdminListConfigurator(
                $this->getEntityManager(),
                $this->get('security.authorization_checker'),
                $this->container->getParameter('hgabka_kunstmaan_email.editor_role')
            );
        }

        return $this->configurator;
    }

    /**
     * The index action.
     *
     * @Route("/", name="hgabkakunstmaanemailbundle_admin_message")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted($this->container->getParameter('hgabka_kunstmaan_banner.editor_role'));

        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The add action.
     *
     * @Route("/add", name="hgabkakunstmaanemailbundle_admin_message_add")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * The edit action.
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_edit")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The edit action.
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_view")
     * @Method({"GET"})
     *
     * @return array
     */
    public function viewAction(Request $request, $id)
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The delete action.
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The export action.
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx"}, name="hgabkakunstmaanemailbundle_admin_message_export")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
}
