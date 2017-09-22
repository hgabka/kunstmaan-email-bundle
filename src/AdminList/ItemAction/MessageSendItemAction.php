<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MessageSendItemAction implements ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return [
            'path' => 'hgabkakunstmaanemailbundle_admin_message_prepare',
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
        return 'hgabka_kuma_email.labels.save_and_send';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return 'send';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:Message:send_item_action.html.twig';
    }
}
