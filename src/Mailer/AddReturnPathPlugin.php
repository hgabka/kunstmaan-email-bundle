<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.07.
 * Time: 8:58
 */

namespace Hgabka\KunstmaanEmailBundle\Mailer;

class AddReturnPathPlugin implements \Swift_Events_SendListener
{
    /** @var  array */
    protected $config;

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return AddHeadersPlugin
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param \Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        if (!($path = $this->config))
        {
            return;
        }

        $message->setReturnPath($path);
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param \Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
    }
}