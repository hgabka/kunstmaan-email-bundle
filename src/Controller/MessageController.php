<?php

namespace Hgabka\KunstmaanEmailBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MessageController extends Controller
{
    /**
     * The webversion action.
     *
     * @Route("/{id}/webversion", requirements={"id" = "\d+"}, name="hgabka_kunstmaan_email_message_webversion")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function webversionAction(Request $request, $id)
    {
        $message = $this->getDoctrine()->getRepository('HgabkaKunstmaanEmailBundle:Message')->find($id);
        if (!$message) {
            throw $this->createNotFoundException('Invalid message');
        }
        $culture = $this->get('hgabka_kunstmaan_extension.kuma_utils')->getCurrentLocale();

        $params = ['nev' => 'XXX', 'email' => 'test@test.com'];
        $params['webversion'] = $this->get('router')->generate('hgabka_kunstmaan_email_message_webversion', ['id' => $message->getId(), '_locale' => $culture], UrlGeneratorInterface::ABSOLUTE_URL);

        $unsubscribeUrl = $this->get('router')->generate('hgabka_kunstmaan_email_message_unsubscribe', ['token' => 'XXX', '_locale' => $culture], UrlGeneratorInterface::ABSOLUTE_URL);
        $unsubscribeLink = '<a href="'.$unsubscribeUrl.'">'.$this->get('translator')->trans('hgabka_kunstmaan_email.message_unsubscribe_default_text').'</a>';
        $params['unsubscribe'] = $unsubscribeUrl;
        $params['unsubscribe_link'] = $unsubscribeLink;

        $substituter = $this->get('hgabka_kunstmaan_email.param_substituter');

        $subject = $substituter->substituteParams($message->translate($culture)->getSubject(), $params);

        $bodyHtml = $substituter->substituteParams($message->translate($culture)->getContentHtml(), $params);

        if ($this->get('hgabka_kunstmaan_email.mail_builder')->getConfig()['auto_append_unsubscribe_link'] && !empty($unsubscribeLink)) {
            $bodyHtml .= '<br /><br />'.$unsubscribeLink;
        }

        $layout = $message->getLayout();

        if ($layout && strlen($bodyHtml) > 0) {
            $bodyHtml = strtr($layout->getDecoratedHtml($culture, $subject), [
                '%%tartalom%%' => $bodyHtml,
                '%%nev%%' => isset($params['nev']) ? $params['nev'] : '',
                '%%email%%' => isset($params['email']) ? $params['email'] : '',
                '%%host%%' => $this->$this->get('hgabka_kunstmaan_extension.kuma_utils')->getSchemeAndHttpHost(),
            ]);
        } elseif (strlen($bodyHtml) > 0 && false !== $this->get('hgabka_kunstmaan_email.mail_builder')->getConfig()['layout_file']) {
            $layoutFile = $this->get('hgabka_kunstmaan_email.mail_builder')->getConfig()['layout_file'];

            if (false !== $layoutFile && !is_file($layoutFile)) {
                $layoutFile = $substituter->getDefaultLayoutPath();
            }

            if (!empty($layoutFile)) {
                $layoutFile = strtr($layoutFile, ['%culture%' => $culture]);
                $html = @file_get_contents($layoutFile);
            } else {
                $html = null;
            }
            if (!empty($html)) {
                $bodyHtml = $this->applyLayout($html, $subject, $bodyHtml, isset($params['nev']) ? $params['nev'] : '', isset($params['email']) ? $params['email'] : '');
            }
        }

        return new Response($bodyHtml);
    }

    /**
     * The unsubscribe action.
     *
     * @Route("/unsubscribe/{token}", name="hgabka_kunstmaan_email_message_unsubscribe")
     *
     * @param Request $request
     * @param mixed   $token
     *
     * @return Response
     */
    public function unsubscribeAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $subscr = $em
            ->getRepository('HgabkaKunstmaanEmailBundle:MessageSubscriber')
            ->findOneByToken($token)
        ;

        if (!$subscr) {
            throw new $this->createNotFoundException('Missing subscriber');
        }

        if ($request->query->has('list_id')) {
            $list = $em
                ->getRepository('HgabkaKunstmaanEmailBundle:MessageList')
                ->findOneById($request->query->get('list_id'))
            ;

            if ($list) {
                $sub = $em
                    ->getRepository('HgabkaKunstmaanEmailBundle:MessageListSubscription')
                    ->findForSubscriberAndList($subscr, $list)
                ;

                if ($sub) {
                    $em->remove($sub);
                    $em->flush();
                }
            }
        } else {
            $em->remove($subscr);
            $em->flush();
        }

        return $this->render('HgabkaKunstmaanEmailBundle:Message:unsubscribe.html.twig');
    }

    /**
     * @param $layout
     * @param $subject
     * @param $bodyHtml
     * @param $name
     * @param $email
     *
     * @return string
     */
    protected function applyLayout($layout, $subject, $bodyHtml, $name, $email)
    {
        if (empty($name)) {
            $name = $this->get('translator')->trans($this->get('hgabka_kunstmaan_email.mail_builder')->getConfig()['default_name']);
        }

        return strtr($layout, [
            '%%host%%' => $this->get('hgabka_kunstmaan_extension.kuma_utils')->getSchemeAndHttpHost(),
            '%%styles%%' => '',
            '%%title%%' => $subject,
            '%%content%%' => $bodyHtml,
            '%%name%%' => $name,
            '%%email%%' => $email,
        ]);
    }
}
