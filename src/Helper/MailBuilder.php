<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.04.
 * Time: 8:25
 */

namespace Hgabka\KunstmaanEmailBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
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

    /** @var  MessageLogger */
    protected $logger;

    /** @var  array */
    protected $config;

    /** @var bool */
    protected $forceLog = false;

    /** @var  RequestStack */
    protected $requestStack;

    /** @var  ParamSubstituter */
    protected $paramSubstituter;

    /** @var  \Swift_Mailer */
    protected $mailer;

    /** @var Translator  */
    protected $translator;

    /**
     * MailBuilder constructor.
     * @param Registry $doctrine
     * @param MessageLogger $logger
     */
    public function __construct(Registry $doctrine, \Swift_Mailer $mailer, RequestStack $requestStack, ParamSubstituter $paramSubstituter, Translator $translator, MessageLogger $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->paramSubstituter = $paramSubstituter;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function log($message)
    {
        if (!$this->forceLog && !$this->config['message_logging']) {
            return false;
        }

        $this->logger->getLogger()->info($message);
    }

    /**
     * @return bool
     */
    public function isForceLog(): bool
    {
        return $this->forceLog;
    }

    /**
     * @param bool $forceLog
     * @return MailBuilder
     */
    public function setForceLog($forceLog)
    {
        $this->forceLog = $forceLog;

        return $this;
    }

    protected function sendEmails($limit = null)
    {
        if (empty($limit)) {
            $limit = $this->config['send_limit'];
        }

        $this->log('Uzenetek kuldese (limit: ' . $limit . ')');

        $count = $sent = $fail = 0;

        $queueRepo = $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:EmailQueue');
        $errorQueues = $queueRepo->getErrorQueuesForSend($limit);

        foreach ($errorQueues as $queue) {
            $count++;
            $to = unserialize($queue->getTo());

            $email = is_array($to) ? key($to) : $to;

            if ($queue->send()) {
                $this->log('Sikertelen kuldes ujra. Email kuldese sikeres. Email: ' . $email);
                $this->doctrine->getManager()->remove($clear);
                $sent++;
            } else {
                $this->log('Sikertelen kuldes ujra. Email kuldes sikertelen. Email: ' . $email . ' Hiba: ' . $queue->getLastError());
                $fail++;
            }
        }

        if ($sent >= $limit) {
            $this->log('Limit elerve, kuldes vege');

            return ['total' => $count, 'sent' => $sent, 'fail' => $fail];
        }

        $queues = $queueRepo->getNotSentQueuesForSend($limit - $sent);

        foreach ($queues as $queue) {
            $count++;
            $to = unserialize($queue->getTo());

            $email = is_array($to) ? key($to) : $to;
            if ($queue->send()) {
                $this->log('Email kuldese sikeres. Email: ' . $email);

                $days = $this->config['sent_messages_after'];

                if (empty($days)) {
                    $queue->delete();
                }
                $sent++;
            } else {
                $this->log('Email kuldes sikertelen. Email: ' . $email . ' Hiba: ' . $queue->getLastError());
                $fail++;
            }
        }

        if ($count >= $limit) {
            $this->log('Limit elerve, kuldes vege');
        } else {
            $this->log('Nincs tobb kuldendo email, kuldes vege');
        }

        return ['total' => $count, 'sent' => $sent, 'fail' => $fail];
    }

    public function getDefaultFrom()
    {
        return $this->translateEmailAddress($this->config['default_sender']);
    }

    public function getDefaultTo()
    {
        return $this->translateEmailAddress($this->config['default_recipient']);
    }

    /**
     * email cím formázás
     *
     * @param array $adress
     *
     * @return string
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

        $mailer = $this->getContext()->getMailer();
        $mail = new \Swift_Message($subject);

        $bodyText = $this->paramSubstituter->substituteParams($template->translate($culture)->getContentText(), $params, true);
        $bodyHtml = $template->translate($culture)->getContentHtml();

        $layout = $template->getLayout();

        if ($layout && strlen($bodyHtml) > 0) {
            $bodyHtml = strtr($layout->getDecoratedHtml($culture, $subject), [
                '%%tartalom%%' => $bodyHtml,
                '%%nev%%'      => $name,
                '%%email%%'    => $email,
                '%%host%%'     => $this->requestStack->getCurrentRequest()->getHost(),
            ]);
        } elseif (strlen($bodyHtml) > 0 && (false !== sfConfig::get('app_hgEmailPlugin_layout_file') || !empty($parameters['layout_file']))) {
            $layoutFile = !empty($parameters['layout_file']) || (isset($parameters['layout_file']) && false === $parameters['layout_file']) ? $parameters['layout_file'] : $this->config['layout_file'];

            if (false !== $layoutFile && !is_file($layoutFile)) {
                $locator = new FileLocator(__DIR__ . '/../../Resources/layout');
                $layoutFile = $locator->locate('layout.html');
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
            /** @var Media $media */
            $media = $attachment->getMedia();

            if ($media) {
                $mail->attach(
                    Swift_Attachment::fromPath($media->getLocation())->setFilename($media->getOriginalFilename())
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
                        $part = Swift_Attachment::fromPath($attachment);
                    } else {
                        $filename = isset($attachment['path']) ? $attachment['path'] : '';
                        if (!is_file($filename)) {
                            continue;
                        }
                        $part = Swift_Attachment::fromPath($filename);
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
        } catch (Exception $e) {
            return false;
        }
    }

    public function enqueueTemplateMessage(EmailTemplate $template, $params = array(), $culture = null, $sendAt = null, $campaign = false)
    {
        if (empty($culture)) {
            $culture = $this->requestStack->getCurrentRequest()->getLocale();
        }
        $message = $this->createTemplateMessage($template, $params, $culture);
        if (!$message)
        {
            return false;
        }
        $attachments = $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByTemplate($template, $culture);

        return hgEmailQueueTable::getInstance()->addMessageToQueue($message, $attachments, $sendAt, $campaign);
    }

    public function sendTemplateMessage(hgEmailTemplate $template, $params = array(), $culture = null)
    {
        if (sfConfig::get('app_hgEmailPlugin_force_queueing', false))
        {
            return $this->enqueueTemplateMessage($template, $params, $culture, null);
        }

        $message = $this->createTemplateMessage($template, $params, $culture);

        if (!$message)
        {
            return false;
        }


        return $this->getContext()->getMailer()->send($message);
    }

    protected function applyLayout($layout, $subject, $bodyHtml, $name, $email)
    {
        if (empty($name))
        {
            $name = $this->translator->trans($this->config['default_name']);
        }

        return strtr($layout, array(
            '%%host%%' => $this->requestStack->getCurrentRequest()->getHost(),
            '%%styles%%' => '',
            '%%title%%' => $subject,
            '%%content%%' => $bodyHtml,
            '%%name%%' => $name,
            '%%email%%' => $email,

        ));
    }

}
