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
     * @param MessageLogger $logger
     */
    public function __construct(Registry $doctrine, \Swift_Mailer $mailer, RequestStack $requestStack, QueueManager $queueManager, ParamSubstituter $paramSubstituter, Translator $translator, MessageLogger $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
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
     * @param $message
     * @return bool
     */
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
     * @return MessageSender
     */
    public function setForceLog($forceLog) : MessageSender
    {
        $this->forceLog = $forceLog;

        return $this;
    }
}