<?php

namespace Hgabka\KunstmaanEmailBundle\Logger;

use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;

class MessageLogger
{
    protected $logger;

    public function __construct(LoggerInterface $logger, $path)
    {
        $this->logger = $logger;
        $handler = new StreamHandler($path.'/'.date('Ymd').'.log');
        $handler->setFormatter(new MessageLogFormatter());
        $this->logger->setHandlers([$handler]);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return MessageLogger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
