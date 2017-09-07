<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.06.
 * Time: 10:26
 */

namespace Hgabka\KunstmaanEmailBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Hgabka\KunstmaanEmailBundle\Logger\MessageLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;

class MessageSender
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

    /** @var bool  */
    protected $forceLog = false;

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
     * @return bool
     */
    public function isForceLog(): bool
    {
        return $this->forceLog;
    }

    /**
     * @param bool $forceLog
     * @return MessageSender
     */
    public function setForceLog($forceLog)
    {
        $this->forceLog = $forceLog;

        return $this;
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

    public function deleteMessageFromQueue($message)
    {
        if (!$message)
        {
            return;
        }

        $this->queueManager->deleteMessageFromQueue($message);
    }

    public function addMessageToQueue($message)
    {
        if (!$message)
        {
            return;
        }

        $this->queueManager->addMessageToQueue($message, $this->getRecipientsForMessage($message));
        $message->setStatus(hgMessageTable::MESSAGE_STATUS_ENQUEUED);
        $this->updateMessageSendData($message);
    }


    public function getDefinedListRecipients($lists)
    {
        $lists = $this->getDefinedRecipientLists($lists);

        if (empty($lists))
        {
            return array();
        }

        $recs = array();
        $recipientsConfig = sfConfig::get('app_hgEmailPlugin_pre_defined_message_recipients', null);

        foreach ($lists as $list)
        {
            $config = $recipientsConfig[$list];

            if (!isset($config['data']) || !is_callable($config['data']))
            {
                continue;
            }

            $data = call_user_func($config['data']);

            if (!is_array($data))
            {
                continue;
            }
            $realData = array();
            foreach ($data as $row)
            {
                $oneRow = array();
                if (isset($row['to']))
                {
                    $oneRow['to'] = $row['to'];
                }
                elseif (isset($row['email']))
                {
                    if (isset($row['name']))
                    {
                        $oneRow['to'] = array($row['email'] => $row['name']);
                    }
                    else
                    {
                        $oneRow['to'] = $row['email'];
                    }
                }

                if (!isset($oneRow['to']))
                {
                    continue;
                }

                if (!isset($oneRow['culture']))
                {
                    $oneRow['culture'] = sfConfig::get('sf_default_culture');
                }

                foreach (array_keys($row) as $other)
                {
                    if (!in_array($other, array('to', 'culture', 'email', 'name')))
                    {
                        $oneRow[$other] = $row[$other];
                    }
                }

                $realData[] = $oneRow;
            }

            $recs = array_merge($recs, $realData);
        }

        return $recs;
    }


    public function getRecipientsForMessage($message)
    {
        $recs = array();
        foreach (sfConfig::get('sf_enabled_cultures') as $culture)
        {
            $tos = $this->getTos($message->getTo(), $culture);
            foreach ($tos as $to)
            {
                $recs[] = $to;
            }
        }

        $definedListRecipients = $this->getDefinedListRecipients($message->getToType());
        if (!empty($definedListRecipients))
        {
            foreach ($definedListRecipients as $listRec)
            {
                $recs[] = $listRec;
            }
        }

        $listRecipients = $this->getListRecipientsForMessage($message);
        if (!empty($listRecipients))
        {
            foreach ($listRecipients as $listRec)
            {
                $recs[] = $listRec;
            }
        }

        return $recs;
    }

    public function getListsForMessage($message)
    {
        $lists = array();
        $sendLists = $message->getSendLists();
        foreach ($sendLists as $sendList)
        {
            $lists[] = $sendList->getList()->getName();
        }

        return $lists;
    }

    public function getListRecipientsForMessage($message)
    {
        $recs = array();

        $sendLists = $message->getSendLists();
        $emails = array();
        foreach ($sendLists as $sendList)
        {
            foreach ($sendList->getList()->getListSubscriptions() as $listSubscription)
            {
                $subscriber = $listSubscription->getSubscriber();
                if (!$subscriber->exists())
                {
                    $listSubscription->delete();
                    continue;
                }

                if (!in_array($subscriber->getEmail(), $emails))
                {
                    $ar = $subscriber->toArray();
                    $recs[] = array_merge($ar, array('to' => array($subscriber->getEmail() => $subscriber->getName()), 'culture' => $subscriber->getCulture()));
                    $emails[] = $subscriber->getEmail();
                }
            }
        }

        return $recs;
    }

    public function sendMessage(hgMessage $message, $forceSend = false)
    {
        $sendAt = $message->getSendAt();
        $sentAt = $message->getSentAt();
        $sent = 0;
        $fail = 0;

        if (((!is_null($sendAt) && $sendAt > date('Y-m-d H:i:s')) || !empty($sentAt)) && !$forceSend)
        {
            return;
        }

        $mailer = $this->getContext()->getMailer();
        $recs = $this->getRecipientsForMessage($message);

        foreach ($recs as $rec)
        {
            if (!isset($rec['to']))
            {
                continue;
            }
            $to = $rec['to'];
            $culture = isset($rec['culture']) ? $rec['culture'] : sfConfig::get('sf_default_culture');

            $email = is_array($to) ? key($to) : $to;

            try
            {
                $mail = $this->createMessageMail($message, $to, $culture, true, $rec);
                if ($mailer->send($mail))
                {
                    $this->log('Email kuldese sikeres. Email: '.$email);

                    $message->setSentAt(date('Y-m-d H:i:s'));
                    $sent++;
                }
                else
                {
                    $this->log('Email kuldes sikertelen. Email: '. $email);
                    $fail++;
                }
            }
            catch (Exception $e)
            {
                $this->log('Email kuldes sikertelen. Email: '. $email . ' Hiba: '.$e->getMessage());

                $fail++;
            }
        }

        $message->save();

        return array('sent' => $sent, 'fail' => $fail, 'total' => $sent+$fail);
    }

    public function log($message)
    {
        $this->queueManager->log($message, $this->forceLog);
    }

    public function createMessageMail(hgMessage $message, $to, $culture = null, $addCcs = true, $parameters = array())
    {
        $builder = hgMailBuilder::getInstance();

        $culture = hgUtils::getCurrentCulture($culture);

        $params = is_array($to) ? array('nev' => current($to), 'email' => key($to)) : array('email' => $to);
        $params['webversion'] = $this->getContext()->getController()->genUrl('@hg_message_webversion?id='.$message->getId().'&culture='.$culture, true);

        $subscriber = hgMessageSubscriberTable::getInstance()->findOneByEmail($params['email']);

        $unsubscribeUrl = $subscriber ? $this->getContext()->getController()->genUrl('@hg_message_unsubscribe?token='.$subscriber->getToken(), true) : '';
        $unsubscribeLink = $subscriber ? '<a href="'.$unsubscribeUrl.'">'.$this->getContext()->getI18N()->__('hg_message_unsubscribe_default_text').'</a>' : '';
        $params['unsubscribe'] = $unsubscribeUrl;
        $params['unsubscribe_link'] = $unsubscribeLink;

        foreach ($parameters as $key => $value)
        {
            if (!in_array($key, array('to', 'name', 'email', 'webversion', 'unsubscribe', 'unsubscribe_link')) && is_string($value))
            {
                $params[$key] = $value;
            }
        }
        $mailer = $this->getContext()->getMailer();
        $mail = $mailer->compose();


        $subject = $builder->substituteParams($message->Translation[$culture]->Subject, $params);
        $bodyText = $builder->substituteParams($message->Translation[$culture]->TextBody, $params);
        $bodyHtml = $builder->substituteParams($builder->embedImages($message->Translation[$culture]->HtmlBody, $mail), $params);

        if (sfConfig::get('app_hgEmailPlugin_auto_append_unsubscribe_link') && !empty($unsubscribeLink))
        {
            $bodyHtml.='<br /><br />'.$unsubscribeLink;
        }

        $layout = $message->getLayout();

        if ($layout && $layout->exists() && strlen($bodyHtml) > 0)
        {
            $bodyHtml = strtr($layout->getDecoratedHtml($culture), array(
                '%%tartalom%%' => $bodyHtml,
                '%%nev%%' => isset($params['nev']) ? $params['nev'] : '',
                '%%email%%' => isset($params['email']) ? $params['email'] : '',
                '%%host%%' => $this->getContext()->getRequest()->getHost()));
        }


        if (strlen($bodyText) > 0)
        {
            $mail->addPart($bodyText, 'text/plain');
        }

        if (strlen($bodyHtml) > 0)
        {
            $mail->addPart($bodyHtml, 'text/html');
        }

        $attachments = $message->getAttachments($culture);

        foreach ($attachments as $attachment)
        {
            $repoFile = hgabkaFileTable::getInstance()->findOneById($attachment->getFileId());

            if ($repoFile)
            {
                $mail->attach(
                    Swift_Attachment::fromPath(hgabkaFileRepository::getInstance()->getFilePath($repoFile))->setFilename($repoFile->getOriginalFilename())
                );
            }
        }

        $mail
            ->setSubject($subject)
            ->setTo($to);

        $name = $message->getFromName();
        $from = empty($name) ? $message->getFromEmail() : array($message->getFromEmail() => $name);
        $mail->setFrom($from);

        if ($addCcs)
        {
            $cc = $message->getCc();

            if (!empty($cc))
            {
                foreach ($this->getTos($cc) as $oneCcData)
                {
                    if (!isset($oneCcData['to']))
                    {
                        continue;
                    }
                    $oneCc = $oneCcData['to'];

                    if (is_array($oneCc))
                    {
                        $mail->addCc(key($oneCc), current($oneCc));
                    }
                    else
                    {
                        $mail->addCc($oneCc);
                    }
                }
            }

            $bcc = $message->getBcc();

            if (!empty($bcc))
            {
                foreach ($this->getTos($bcc) as $oneBccData)
                {
                    if (!isset($oneBccData['to']))
                    {
                        continue;
                    }
                    $oneBcc = $oneBccData['to'];
                    if (is_array($oneBcc))
                    {
                        $mail->addCc(key($oneBcc), current($oneBcc));
                    }
                    else
                    {
                        $mail->addCc($oneBcc);
                    }
                }
            }
        }


        return $mail;
    }

    public function prepareMessage($message)
    {
        $sendAt = $message->getSendAt();
        $message->setStatus(hgMessageTable::MESSAGE_STATUS_PREPARED);

        if (empty($sendAt) || $sendAt < date('Y-m-d H:i:s'))
        {
            $this->addMessageToQueue($message);
        }
        else
        {
            $message->save();
        }
    }

    public function updateMessageSendData($message)
    {
        if (!$message || $message->getStatus() != hgMessageTable::MESSAGE_STATUS_ENQUEUED)
        {
            return;
        }

        $sendData = hgMessageQueueTable::getInstance()->getSendDataForMessage($message->getId());

        $message->setSentMail($sendData['sum']);
        $message->setSentSuccess($sendData[hgMessageQueueTable::QUEUE_STATUS_SENT]);
        $message->setSentFail($sendData[hgMessageQueueTable::QUEUE_STATUS_FAIL]);

        if ($sendData['sum'] == $sendData[hgMessageQueueTable::QUEUE_STATUS_SENT] +  $sendData[hgMessageQueueTable::QUEUE_STATUS_FAIL] +  $sendData[hgMessageQueueTable::QUEUE_STATUS_BOUNCED])
        {
            $message->setStatus(hgMessageTable::MESSAGE_STATUS_SENT);
            $days = sfConfig::get('app_hgEmailPlugin_delete_sent_messages_after', null);

            if (empty($days))
            {
                $this->deleteMessageFromQueue($message);
            }
        }

        $message->save();
    }

    public function prepareMessages()
    {
        $messages = hgMessageTable::getInstance()->getMessagesToQueue();

        foreach ($messages as $message)
        {
            $this->prepareMessage($message);
        }
    }

    public function updateMessages()
    {
        $messages = hgMessageTable::getInstance()->getMessagesToUpdate();

        foreach ($messages as $message)
        {
            $this->updateMessageSendData($message);
        }
    }

    public function sendQueue(?int $limit = null)
    {
        $this->prepareMessages();

        $result = $this->queueManager->sendMessages($limit);

        $this->updateMessages();

        return $result;
    }

    public function unPrepareMessage($message)
    {
        if (!$message)
        {
            return;
        }

        $this->deleteMessageFromQueue($message);
        $message->setSentMail(0);
        $message->setSentSuccess(0);
        $message->setSentFail(0);
        $message->setStatus(hgMessageTable::MESSAGE_STATUS_INIT);

        $message->save();
    }

    public function getTos($tos, $culture = null)
    {
        $toArray = explode("\r\n", trim($tos, "\r\n"));

        $recs = array();
        $culture = hgUtils::getCurrentCulture($culture);

        foreach ($toArray as $oneTo)
        {
            $oneTo = trim($oneTo, "\r\n");

            if (false !== strpos($oneTo, ':'))
            {
                $parts = explode(':', $oneTo);
                $email = trim($parts[1]);
                $to = array($email => trim($parts[0]));
            }
            else
            {
                $to = trim($oneTo);
            }

            if (!empty($to))
            {
                $recs[] = array('to' => $to, 'culture' => $culture);
            }
        }

        return $recs;
    }

    public function getDefinedRecipientLists($lists)
    {
        $recipientsConfig = $this->config['pre_defined_message_recipients'];
        $lists = explode("\r\n", $lists);

        if (empty($recipientsConfig) || !is_array($recipientsConfig) || empty($lists))
        {
            return array();
        }

        return array_intersect(array_keys($recipientsConfig), $lists);

    }

    public function sendMessages(?int $limit = null)
    {
        if (empty($limit))
        {
            $limit = $this->config['send_limit'];
        }

        $this->log('Uzenetek kuldese (limit: '.$limit.')');
        $messages = $this->getMessageRepository()->getMessagesToSend();

        $sent = 0;
        $fail = 0;

        foreach ($messages as $message)
        {
            $result = $this->sendMessage($message);

            $sent+= $result['sent'];
            $fail+= $result['fail'];

            if ($sent >= $limit)
            {
                $this->log('Limit elerve, kuldes vege');

                return array('sent' => $sent, 'fail' => $fail, 'total' => $sent+$fail);
            }
        }

        $this->log('Nincs tobb kuldendo email, kuldes vege');

        return array('sent' => $sent, 'fail' => $fail, 'total' => $sent+$fail);
    }

    protected function getQueueRepository()
    {
        return $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:MessageQueue');
    }

    protected function getMessageRepository()
    {
        return $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:Message');
    }

    public function deleteEmailFromQueue($email)
    {
        return $this->getQueueRepository()->deleteEmailFromQueue($email);
    }
}