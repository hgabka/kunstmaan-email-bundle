<?php

namespace Hgabka\KunstmaanEmailBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MessageUnprepareItemListAction implements ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return [
            'path' => 'hgabkakunstmaanemailbundle_admin_message_unprepare',
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
        return 'hgabka_kuma_email.labels.unprepare';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return 'ban';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'HgabkaKunstmaanEmailBundle:AdminList:Message\_unprepare_item_list_action.html.twig';
    }
}
