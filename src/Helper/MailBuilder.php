<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.04.
 * Time: 8:25
 */

namespace Hgabka\KunstmaanEmailBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Hgabka\KunstmaanEmailBundle\Entity\Attachment;
use Hgabka\KunstmaanEmailBundle\Logger\MessageLogger;
use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplate;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Translation\Translator;

class MailBuilder
{
    /** @var Registry */
    protected $doctrine;

    /** @var  array */
    protected $config;

    /** @var  RequestStack */
    protected $requestStack;

    /** @var  ParamSubstituter */
    protected $paramSubstituter;

    /** @var  \Swift_Mailer */
    protected $mailer;

    /** @var Translator */
    protected $translator;

    /** @var  QueueManager */
    protected $queueManager;

    /**
     * MailBuilder constructor.
     * @param Registry $doctrine
     * @param \Swift_Mailer $mailer
     * @param RequestStack $requestStack
     * @param QueueManager $queueManager
     * @param ParamSubstituter $paramSubstituter
     * @param Translator $translator
     */
    public function __construct(Registry $doctrine, \Swift_Mailer $mailer, RequestStack $requestStack, QueueManager $queueManager, ParamSubstituter $paramSubstituter, Translator $translator)
    {
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
        $this->paramSubstituter = $paramSubstituter;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->queueManager = $queueManager;
    }

    /**
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param int|null $limit
     * @return array
     */
    public function sendQueue(?int $limit = null)
    {
        return $this->queueManager->sendEmails($limit);
    }

    /**
     * @return array|string
     */
    public function getDefaultFrom()
    {
        return $this->translateEmailAddress($this->config['default_sender']);
    }

    /**
     * @return array|string
     */
    public function getDefaultTo()
    {
        return $this->translateEmailAddress($this->config['default_recipient']);
    }

    /**
     * email cím formázás
     *
     * @param array $address
     *
     * @return string|array
     */
    public function translateEmailAddress($address)
    {
        if (is_string($address) || ((!isset($address['name']) || strlen($address['name']) == 0) && (!isset($address['email']) || strlen($address['email']) == 0))) {
            return $address;
        }

        if (isset($address['name']) && strlen($address['name'])) {
            return [$address['email'] => $address['name']];
        } else {
            return $address['email'];
        }
    }

    public function createTemplateMessage(EmailTemplate $template, $parameters = [], $culture = null)
    {
        $parameters['from'] = empty($parameters['from']) ? $this->getDefaultFrom() : $parameters['from'];
        $parameters['to'] = empty($parameters['to']) ? $this->getDefaultTo() : $parameters['to'];

        if (empty($parameters['from']) || empty($parameters['to'])) {
            return false;
        }
        $params = $this->paramSubstituter->normalizeParams(empty($parameters['params']) ? [] : $parameters['params']);
        $to = $this->translateEmailAddress($parameters['to']);

        $name = is_array($to) ? current($to) : '';
        $email = is_array($to) ? key($to) : $to;
        if (!isset($params['nev'])) {
            $params['nev'] = $name;
        }

        if (!isset($params['email'])) {
            $params['email'] = $email;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (empty($culture)) {
            $culture = $request->getLocale();
        }

        $subject = $this->paramSubstituter->substituteParams($template->translate($culture)->getSubject(), $params, true);

        $mail = new \Swift_Message($subject);

        $bodyText = $this->paramSubstituter->substituteParams($template->translate($culture)->getContentText(), $params, true);
        $bodyHtml = $template->translate($culture)->getContentHtml();

        $layout = $template->getLayout();

        if ($layout && strlen($bodyHtml) > 0) {
            $layoutFile = $this->config['layout_file'];
            if ($layoutFile === false)
            {
                $layoutFile = null;
            }
            elseif (empty($layoutFile))
            {
                $layoutFile = $this->getDefaultLayoutPath();
            }

            $bodyHtml = strtr($layout->getDecoratedHtml($culture, $subject, $layoutFile), [
                '%%tartalom%%' => $bodyHtml,
                '%%nev%%'      => $name,
                '%%email%%'    => $email,
                '%%host%%'     => $this->requestStack->getCurrentRequest()->getHost(),
            ]);
        } elseif (strlen($bodyHtml) > 0 && (false !== $this->config['layout_file'] || !empty($parameters['layout_file']))) {
            $layoutFile = !empty($parameters['layout_file']) || (isset($parameters['layout_file']) && false === $parameters['layout_file']) ? $parameters['layout_file'] : $this->config['layout_file'];

            if (false !== $layoutFile && !is_file($layoutFile)) {
                $layoutFile = $this->getDefaultLayoutPath();
            }

            if (!empty($layoutFile)) {
                $layoutFile = strtr($layoutFile, ['%culture%' => $culture]);
                $html = @file_get_contents($layoutFile);
            } else {
                $html = null;
            }
            if (!empty($html)) {
                $bodyHtml = $this->applyLayout($html, $subject, $bodyHtml, $name, $email);
            }
        }

        if (strlen($bodyText) > 0) {
            $mail->addPart($bodyText, 'text/plain');
        }

        if (strlen($bodyHtml) > 0) {
            $bodyHtml = $this->paramSubstituter->embedImages($this->paramSubstituter->substituteParams($bodyHtml, $params, true), $mail);
            $mail->addPart($bodyHtml, 'text/html');
        }

        $attachments = $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByTemplate($template, $culture);

        foreach ($attachments as $attachment) {
            /** @var Attachment $attachment */
            $media = $attachment->getMedia();

            if ($media) {
                $mail->attach(
                    \Swift_Attachment::newInstance($media->getContent(), $media->getOriginalFilename(), $media->getContentType())
                );
            }
        }

        try {
            $mail->setFrom($this->translateEmailAddress($parameters['from']));
            $mail->setTo($this->translateEmailAddress($parameters['to']));

            if (!empty($parameters['cc'])) {
                $mail->setCc($this->translateEmailAddress($parameters['cc']));
            }

            if (!empty($parameters['bcc'])) {
                $mail->setBcc($this->translateEmailAddress($parameters['bcc']));
            }

            if (!empty($parameters['attachments'])) {
                $attachments = $parameters['attachments'];
                if (is_string($attachments)) {
                    $attachments = [$attachments];
                }

                foreach ($attachments as $attachment) {
                    if (is_string($attachment)) {
                        if (!is_file($attachment)) {
                            continue;
                        }
                        $part = \Swift_Attachment::fromPath($attachment);
                    } else {
                        $filename = isset($attachment['path']) ? $attachment['path'] : '';
                        if (!is_file($filename)) {
                            continue;
                        }
                        $part = \Swift_Attachment::fromPath($filename);
                        if (isset($attachment['filename'])) {
                            $part->setFilename($attachment['filename']);
                        }
                        if (isset($attachment['mime'])) {
                            $part->setContentType($attachment['mime']);
                        }
                        if (isset($attachment['disposition'])) {
                            $part->setDisposition($attachment['disposition']);
                        }
                    }

                    $mail->attach($part);
                }
            }

            return $mail;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array|string
     */
    protected function getDefaultLayoutPath()
    {
        $locator = new FileLocator(__DIR__ . '/../../Resources/layout');

        return $locator->locate('layout.html');
    }

    /**
     * @param EmailTemplate $template
     * @param array $params
     * @param null $culture
     * @param null $sendAt
     * @param bool $campaign
     * @return bool|mixed
     */
    public function enqueueTemplateMessage(EmailTemplate $template, $params = [], $culture = null, $sendAt = null, $campaign = false)
    {
        if (empty($culture)) {
            $culture = $this->requestStack->getCurrentRequest()->getLocale();
        }
        $message = $this->createTemplateMessage($template, $params, $culture);
        if (!$message) {
            return false;
        }
        $attachments = $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByTemplate($template, $culture);

        return $this->queueManager->addEmailMessageToQueue($message, $attachments, $sendAt, $campaign);
    }

    /**
     * @param EmailTemplate $template
     * @param array $params
     * @param null $culture
     * @return bool|int|mixed
     */
    public function sendTemplateMessage(EmailTemplate $template, $params = [], $culture = null)
    {
        if ($this->config['force_queueing']) {
            return $this->enqueueTemplateMessage($template, $params, $culture, null);
        }

        $message = $this->createTemplateMessage($template, $params, $culture);

        if (!$message) {
            return false;
        }

        return $this->mailer->send($message);
    }

    /**
     * @param $layout
     * @param $subject
     * @param $bodyHtml
     * @param $name
     * @param $email
     * @return string
     */
    protected function applyLayout($layout, $subject, $bodyHtml, $name, $email)
    {
        if (empty($name)) {
            $name = $this->translator->trans($this->config['default_name']);
        }

        return strtr($layout, [
            '%%host%%'    => $this->requestStack->getCurrentRequest()->getHost(),
            '%%styles%%'  => '',
            '%%title%%'   => $subject,
            '%%content%%' => $bodyHtml,
            '%%name%%'    => $name,
            '%%email%%'   => $email,

        ]);
    }

    /**
     * @param string $name
     * @return EmailTemplate|null
     */
    public function getTemplateByName(string $name) : ?EmailTemplate
    {
        if (empty($name))
        {
            return null;
        }

        return $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:EmailTemplate')->findOneBy(['name' => $name]);
    }

    /**
     * @param $slug
     * @return null|EmailTemplate
     */
    public function getTemplateBySlug(string $slug) : ?EmailTemplate
    {
        if (empty($slug))
        {
            return null;
        }

        return $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:EmailTemplate')->findOneBy(['slug' => $slug]);
    }

    public function sendTemplateMail($name, $params = array(), $culture = null)
    {
        $template = $this->getTemplateBySlug($name);
        if (!$template)
        {
            $template = $this->getTemplateByName($name);
        }

        if (!$template)
        {
            return false;
        }

        return $this->sendTemplateMessage($template, $params, $culture);
    }
}
