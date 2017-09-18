<?php

namespace Hgabka\KunstmaanEmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\KunstmaanEmailBundle\Enum\QueueStatusEnum;
use Hgabka\KunstmaanExtensionBundle\Traits\TimestampableEntity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

class AbstractQueue extends AbstractEntity
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="retries", type="integer")
     */
    protected $retries = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    protected $status = QueueStatusEnum::STATUS_INIT;

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param int $retries
     *
     * @return AbstractQueue
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return AbstractQueue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
