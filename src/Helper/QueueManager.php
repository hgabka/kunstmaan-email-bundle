<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017.09.06.
 * Time: 12:48
 */

namespace Hgabka\KunstmaanEmailBundle\Helper;

use Hgabka\KunstmaanEmailBundle\Entity\AbstractQueue;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Hgabka\KunstmaanEmailBundle\Entity\Attachment;
use Hgabka\KunstmaanEmailBundle\Enum\QueueStatusEnum;
use Kunstmaan\MediaBundle\Entity\Media;

class QueueManager
{
    /** @var Registry */
    protected $doctrine;

    protected $lastError;

    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var  array */
    protected $bounceConfig;

    /** @var int */
    protected $maxRetries;

    public function __construct(Registry $doctrine, \Swift_Mailer $mailer, array $bounceConfig, int $maxRetries)
    {
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
        $this->bounceConfig = $bounceConfig;
        $this->maxRetries = $maxRetries;
    }

    public function send(AbstractQueue $queue)
    {
        $to = unserialize($queue->getTo());
        $from = unserialize($queue->getFrom());
        $cc = $queue->getCc();
        if (!empty($cc)) {
            $cc = unserialize($cc);
        }

        $bcc = $queue->getBcc();
        if (!empty($bcc)) {
            $bcc = unserialize($bcc);
        }

        try {
            $message =
                (new \Swift_Message($queue->getSubject()))
                    ->setFrom($from)
                    ->setTo($to)
            ;

            if (!empty($cc)) {
                $message->setCc($cc);
            }

            if (!empty($bcc)) {
                $message->setBcc($bcc);
            }

            $contentText = $queue->getContentText();
            $contentHtml = $queue->getContentHtml();

            if (!empty($contentText)) {
                $message->setBody($contentText);
            }
            if (!empty($contentHtml)) {
                $message->addPart($contentHtml, 'text/html');
            }

            foreach ($this->getAttachments($queue) as $attachment) {
                $content = $attachment->getContent();

                if ($content) {
                    $message->attach(
                        \Swift_Attachment::newInstance($content, $attachment->getFilename(), $attachment->getContentType())
                    );
                }
            }

            $headers = $message->getHeaders();
            $headers->addTextHeader('hgabka-kunstmaan-email-id', $queue->getId());

            if (isset($this->bounceConfig['account']['address'])) {
                $message->setReturnPath($this->bounceConfig['account']['address']);
            }

            if ($this->mailer->send($message) < 1) {
                $this->setError('Sikertelen küldés', $queue);
                $this->doctrine->getManager()->flush();

                return true;
            } else {
                $queue->setStatus(QueueStatusEnum::STATUS_ELKULDVE);
                $this->doctrine->getManager()->flush();

                return false;
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $queue);
            $this->doctrine->getManager()->flush();

            return false;
        }
    }

    public function getAttachments(AbstractQueue $queue)
    {
        return $this->doctrine->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByQueue($queue);
    }

    public function setError(string $message, AbstractQueue $queue)
    {
        $retries = $queue->getRetries();
        $this->lastError = $message;
        if (empty($retries)) {
            $retries = 0;
        }

        if (++$retries > $this->maxRetries) {
            $queue->setStatus(QueueStatusEnum::STATUS_SIKERTELEN);
            $this->lastError .= "\n$maxRetries probalkozas elerve, sikertelen statusz beallitva";
        } else {
            $queue->setRetries($retries)->setStatus(QueueStatusEnum::STATUS_HIBA);
        }
    }

    public function addMessageToQueue($message, $attachments, $sendAt = null, $campaign = false)
    {
        if (!$message)
        {
            return;
        }

        $queue = new EmailQueue();
        $queue->setSendAt($sendAt);
        $queue->setFrom(serialize($message->getFrom()));
        $queue->setTo(serialize($message->getTo()));
        $queue->setCc($message->getCc());
        $queue->setBcc($message->getBcc());
        $queue->setSubject($message->getSubject());
        $queue->setContentText($message->getBody());

        if ($campaign instanceof EmailCampaign)
        {
            $queue->setCampaign($campaign);
        }

        $children = $message->getChildren();
        foreach ($children as $child)
        {
            if ($child->getContentType() == 'text/html')
            {
                $queue->setContentHtml($child->getBody());
            }

        }

        $em = $this->doctrine->getManager();
        $em->persist($queue);

        foreach ($attachments as $attachment)
        {
            $newAttachment = new Attachment();
            $newAttachment->setType(get_class($queue));
            $newAttachment->setOwnerId($queue->getId());
            /** @var Media $media */
            $media = $attachment->getMedia();
            $newAttachment->setFilename($media->getOriginalFilename());
            $newAttachment->setContent($media->getContent());
            $newAttachment->setContentType($media->getContentType());
            $newAttachment->setLocale($attachment->getLocale());

            $em->persist($newAttachment);
        }

        $em->flush();

        return $message;
    }

    /**
     * @return mixed
     */
    public function getLastError()
    {
        return $this->lastError;
    }
}
