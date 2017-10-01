<?php

namespace Hgabka\KunstmaanEmailBundle\Controller;

use Hgabka\KunstmaanEmailBundle\AdminList\MessageAdminListConfigurator;
use Hgabka\KunstmaanEmailBundle\Entity\Attachment;
use Hgabka\KunstmaanEmailBundle\Entity\Message;
use Hgabka\KunstmaanEmailBundle\Entity\MessageSendList;
use Hgabka\KunstmaanEmailBundle\Enum\MessageStatusEnum;
use Hgabka\KunstmaanEmailBundle\Form\MessageMailType;
use Hgabka\KunstmaanEmailBundle\Form\MessageSendType;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                $this->get('hgabka_kunstmaan_email.mail_builder'),
                $this->get('request_stack'),
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
        $configurator = $this->getAdminListConfigurator();
        $configurator->buildItemActions();
        if (!$configurator->canAdd()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        // @var EntityManager $em
        $em = $this->getEntityManager();

        $entityName = null;
        if (isset($type)) {
            $entityName = $type;
        } else {
            $entityName = $configurator->getRepositoryName();
        }

        $classMetaData = $em->getClassMetadata($entityName);
        // Creates a new instance of the mapped class, without invoking the constructor.
        $classname = $classMetaData->getName();
        $helper = new $classname();
        $helper = $configurator->decorateNewEntity($helper);


        $event = new AdaptSimpleFormEvent($request, $configurator->getAdminType($helper), $helper, $configurator->getAdminTypeOptions());
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($configurator->getAdminType($helper), $helper, $configurator->getAdminTypeOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if ($form->isValid() && !$request->isXmlHttpRequest()) {
                $adminListEvent = new AdminListEvent($helper, $request, $form);
                $this->container->get('event_dispatcher')->dispatch(
                    AdminListEvents::PRE_ADD,
                    $adminListEvent
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $em->persist($helper);
                $em->flush();
                $this->container->get('event_dispatcher')->dispatch(
                    AdminListEvents::POST_ADD,
                    $adminListEvent
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                if ($request->request->has('save_and_send')) {
                    return $this->redirectToRoute('hgabkakunstmaanemailbundle_admin_message_prepare', ['id' => $helper->getId()]);
                }

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
                );
            }
        }

        $params = ['form' => $form->createView(), 'adminlistconfigurator' => $configurator];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return new Response(
            $this->renderView($configurator->getAddTemplate(), $params)
        );
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

    /**
     * The prepare action.
     *
     * @param int $id
     *
     * @Route("/{id}/prepare", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_prepare")
     * @Method({"GET", "POST"})
     *
     * @return Response
     */
    public function prepareAction(Request $request, $id)
    {
        // @var $em EntityManager
        $em = $this->getEntityManager();
        $configurator = $this->getAdminListConfigurator();

        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($id);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }
        if (!$configurator->canPrepare($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $form = $this->createForm(MessageSendType::class, $helper);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                $this->get('hgabka_kunstmaan_email.message_sender')->prepareMessage($helper);
                $this->get('session')->getFlashBag()->add('success', 'hgabka_kuma_email.messages.prepare_success');

                return $this->redirectToRoute('hgabkakunstmaanemailbundle_admin_message');
            }
            $this->get('session')->getFlashBag()->add('error', 'hgabka_kuma_email.messages.prepare_error');
        }

        return $this->render('HgabkaKunstmaanEmailBundle:AdminList:Message/send.html.twig', [
            'form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator,
            ]);
    }

    /**
     * The unprepare action.
     *
     * @param int $id
     *
     * @Route("/{id}/unprepare", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_unprepare")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function unprepareAction(Request $request, $id)
    {
        // @var $em EntityManager
        $em = $this->getEntityManager();
        $configurator = $this->getAdminListConfigurator();

        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($id);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }
        if (!$configurator->canUnprepare($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $this->get('hgabka_kunstmaan_email.message_sender')->unPrepareMessage($helper);
        $this->get('session')->getFlashBag()->add('success', 'hgabka_kuma_email.messages.unprepare_success');

        return $this->redirectToRoute('hgabkakunstmaanemailbundle_admin_message');
    }

    /**
     * The testmail action.
     *
     * @param int $id
     *
     * @Route("/{id}/testmail", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_testmail")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function testmailAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted($this->container->getParameter('hgabka_kunstmaan_banner.editor_role'));
        // @var $em EntityManager
        $em = $this->getEntityManager();
        $configurator = $this->getAdminListConfigurator();

        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($id);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }

        $form = $this->createForm(MessageMailType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $email = $form->getData()['email'];

                $message = $this->get('hgabka_kunstmaan_email.mail_builder')
                                ->createMessageMail($helper, [$email => 'XXX'], $form->getData()['locale'], false);

                $this->get('mailer')->send($message);
                $this->get('session')->getFlashBag()->add('success', 'hgabka_kuma_email.messages.testmail_success');

                return $this->redirectToRoute('hgabkakunstmaanemailbundle_admin_message');
            }
            $this->get('session')->getFlashBag()->add('error', 'hgabka_kuma_email.messages.testmail_error');
        }

        return $this->render('HgabkaKunstmaanEmailBundle:AdminList:Message/testmail.html.twig', [
            'form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator,
        ]);
    }

    /**
     * The testmail action.
     *
     * @param int $id
     *
     * @Route("/{id}/copy", requirements={"id" = "\d+"}, name="hgabkakunstmaanemailbundle_admin_message_copy")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function copyAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted($this->container->getParameter('hgabka_kunstmaan_banner.editor_role'));
        // @var $em EntityManager
        $em = $this->getEntityManager();
        $configurator = $this->getAdminListConfigurator();

        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($id);
        if (null === $helper) {
            throw new NotFoundHttpException('Entity not found.');
        }
        $utils = $this->get('hgabka_kunstmaan_extension.kuma_utils');

        $arr = $utils->entityToArray($helper, 0);
        unset($arr['id'], $arr['createdAt'], $arr['updatedAt']);
        $arr['sendAt'] = null;
        $arr['status'] = MessageStatusEnum::STATUS_INIT;

        $message = new Message();
        $utils->entityFromArray($message, $arr);
        $message
            ->setSentMail(0)
            ->setSentFail(0)
            ->setSentSuccess(0)
        ;

        foreach ($utils->getAvailableLocales() as $locale) {
            $message->translate($locale)->setSubject($helper->translate($locale)->getSubject());
            $message->translate($locale)->setContentText($helper->translate($locale)->getContentText());
            $message->translate($locale)->setContentHtml($helper->translate($locale)->getContentHtml());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        $attachments = $em->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByMessage($helper);
        foreach ($attachments as $attachment) {
            $attArr = $utils->entityToArray($attachment, 0);
            unset($attArr['id'], $attArr['ownerId'], $attArr['createdAt'], $attArr['updatedAt']);

            $newAttachment = new Attachment();
            $utils->entityFromArray($newAttachment, $attArr);

            $newAttachment
                ->setOwnerId($message->getId())
                ->setMedia($attachment->getMedia())
            ;

            $em->persist($newAttachment);
        }
        foreach ($helper->getSendLists() as $sendList) {
            $newList = new MessageSendList();
            $newList->setList($sendList->getList());
            $newList->setMessage($message);
            $em->persist($newList);
        }

        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'hgabka_kuma_email.messages.copy_success');

        return $this->redirectToRoute('hgabkakunstmaanemailbundle_admin_message');
    }
}
