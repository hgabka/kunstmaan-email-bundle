<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.06.
 * Time: 21:05
 */

namespace Hgabka\KunstmaanEmailBundle\Mailer;

class RedirectPlugin extends \Swift_Plugins_RedirectingPlugin
{
    /** @var  $redirectConfig */
    protected $redirectConfig;

    /**
     * @return mixed
     */
    public function getRedirectConfig()
    {
        return $this->redirectConfig;
    }

    /**
     * @param mixed $redirectConfig
     * @return RedirectPlugin
     */
    public function setRedirectConfig($redirectConfig)
    {
        $this->redirectConfig = $redirectConfig;

        return $this;
    }
    
    /**
     * Invoked immediately before the Message is sent.
     * @param \Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $headers = $message->getHeaders();

        $headers->addTextHeader('X-Swift-Subject', $message->getSubject());

        // Appending original recipient data to subject
        $redirectConfig = $this->redirectConfig;

        if (isset($redirectConfig['subject_append']) && ($redirectConfig['subject_append'] === true))
        {
            $message->setSubject($message->getSubject()
                .($message->getTo()?(' - Eredeti to: '. $this->recipientToString($message->getTo())):'')
                .($message->getCc()?(' - Eredeti cc: '. $this->recipientToString($message->getCc())):'')
                .($message->getBcc()?(' - Eredeti bcc: '. $this->recipientToString($message->getBcc())):'')
            );
        }

        parent::beforeSendPerformed($evt);

    }

    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        $this->_restoreMessage($evt->getMessage());
    }

    /**
     * Üzenet paraméterek visszaállítása eredetire
     *
     * @param \Swift_Mime_Message $message
     */
    private function _restoreMessage(\Swift_Mime_Message $message)
    {
        // restore original headers
        $headers = $message->getHeaders();

        if ($headers->has('X-Swift-To'))
        {
            $message->setTo($headers->get('X-Swift-To')->getNameAddresses());
            $headers->removeAll('X-Swift-To');
        }

        if ($headers->has('X-Swift-Cc'))
        {
            $message->setCc($headers->get('X-Swift-Cc')->getNameAddresses());
            $headers->removeAll('X-Swift-Cc');
        }

        if ($headers->has('X-Swift-Bcc'))
        {
            $message->setBcc($headers->get('X-Swift-Bcc')->getNameAddresses());
            $headers->removeAll('X-Swift-Bcc');
        }

        if ($headers->has('X-Swift-Subject'))
        {
            $message->setSubject($headers->get('X-Swift-Subject')->getValue());
            $headers->removeAll('X-Swift-Subject');
        }
    }

    /**
     * címzett tömb "név <email>" stringgé konvertálása
     *
     * @param array $recipient
     *
     * @return string
     */
    private function recipientToString($recipient)
    {
        if (empty($recipient))
        {
            return '';
        }

        $result = array();

        foreach ($recipient as $mail => $name)
        {
            if (strlen($name))
            {
                $result[] = $name . ' <' . $mail . '>';
            }
            else
            {
                $result[] = $mail;
            }
        }

        return implode(', ', $result);
    }
}