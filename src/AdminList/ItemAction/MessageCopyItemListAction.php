<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MessageCopyItemListAction implements ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return [
            'path' => 'hgabkakunstmaanemailbundle_admin_message_copy',
            'params' => ['id' => $item->getId()],
        ];
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        return 'hgabka_kuma_email.labels.copy';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return 'files-o';
    }

    public function getTemplate()
    {
        return null;
    }
}
