<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MessageWebItemListAction implements ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return [
            'path' => 'hgabka_kunstmaan_email_message_webversion',
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
        return 'hgabka_kuma_email.labels.webversion';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return 'globe';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:AdminList:Message\_web_item_list_action.html.twig';
    }
}
