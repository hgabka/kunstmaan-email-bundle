<?php

namespace Hgabka\KunstmaanEmailBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class EmailMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /** @var string */
    protected $editorRole;

    /** @var array */
    protected $config;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array                         $config
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, array $config)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->config = $config;
        $this->editorRole = $config['editor_role'];
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children.
     *
     * @param MenuBuilder   $menu      The MenuBuilder
     * @param MenuItem[]    &$children The current children
     * @param null|MenuItem $parent    The parent Menu item
     * @param Request       $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (!$this->authorizationChecker->isGranted($this->editorRole)) {
            return;
        }
        if (null === $parent) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('hgabkakunstmaanemailbundle_admin_emailtemplate');
            $menuItem->setUniqueId('email');
            $menuItem->setLabel('hgabka_kuma_email.titles.menu');
            $menuItem->setParent($parent);

            $newChildren = [];
            $inserted = false;
            foreach ($children as $child) {
                if ('settings' === $child->getUniqueId()) {
                    $newChildren[] = $menuItem;
                    $inserted = true;
                }
                $newChildren[] = $child;
            }
            if (!$inserted) {
                $newChildren[] = $menuItem;
            }

            $children = $newChildren;

            if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                $menuItem->setActive(true);
            }
        } elseif ('email' === $parent->getUniqueId()) {
            if ($this->config['email_templates_enabled']) {
                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('hgabkakunstmaanemailbundle_admin_emailtemplate')
                    ->setUniqueId('email_template')
                    ->setLabel('hgabka_kuma_email.titles.email_template.list')
                    ->setParent($parent)
                ;
                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }

                $children[] = $menuItem;

                $menuItem = new TopMenuItem($menu);
                $menuItem
                    ->setRoute('hgabkakunstmaanemailbundle_admin_emailtemplate')
                    ->setUniqueId('email_template')
                    ->setLabel('hgabka_kuma_email.titles.email_template.list')
                    ->setParent($parent)
                    ->setAppearInNavigation(false)
                ;

                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
            if ($this->config['messages_enabled']) {
                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('hgabkakunstmaanemailbundle_admin_message')
                    ->setUniqueId('message')
                    ->setLabel('hgabka_kuma_email.titles.message.list')
                    ->setParent($parent)
                ;
                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute()) &&
                    false === stripos($request->attributes->get('_route'), 'hgabkakunstmaanemailbundle_admin_messagesubscriber')
                ) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }

                $children[] = $menuItem;

                $menuItem = new TopMenuItem($menu);
                $menuItem
                    ->setRoute('hgabkakunstmaanemailbundle_admin_message')
                    ->setUniqueId('message')
                    ->setLabel('hgabka_kuma_email.titles.message.list')
                    ->setParent($parent)
                    ->setAppearInNavigation(false)
                ;

                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute()) &&
                    false === stripos($request->attributes->get('_route'), 'hgabkakunstmaanemailbundle_admin_messagesubscriber')
                ) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
            if ($this->config['subscribers_enabled']) {
                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('hgabkakunstmaanemailbundle_admin_messagesubscriber')
                    ->setUniqueId('subscriber')
                    ->setLabel('hgabka_kuma_email.titles.subscriber.list')
                    ->setParent($parent)
                ;
                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }

                $children[] = $menuItem;

                $menuItem = new TopMenuItem($menu);
                $menuItem
                    ->setRoute('hgabkakunstmaanemailbundle_admin_messagesubscriber')
                    ->setUniqueId('subscriber')
                    ->setLabel('hgabka_kuma_email.titles.subscriber.list')
                    ->setParent($parent)
                    ->setAppearInNavigation(false)
                ;

                if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
        } elseif ('email_template' === $parent->getUniqueId()) {
            $this->addMenuForSubRoute($menu, $parent, 'Email sablon szerkesztése', 'hgabkakunstmaanemailbundle_admin_emailtemplate_edit', $children, $request);
            $this->addMenuForSubRoute($menu, $parent, 'Új email sablon', 'hgabkakunstmaanemailbundle_admin_emailtemplate_add', $children, $request);
        } elseif ('message' === $parent->getUniqueId()) {
            $this->addMenuForSubRoute($menu, $parent, 'Körlevél szerkesztése', 'hgabkakunstmaanemailbundle_admin_message_edit', $children, $request);
            $this->addMenuForSubRoute($menu, $parent, 'Új körlevél', 'hgabkakunstmaanemailbundle_admin_message_add', $children, $request);
        } elseif ('subscriber' === $parent->getUniqueId()) {
            $this->addMenuForSubRoute($menu, $parent, 'Feliratkozott szerkesztése', 'hgabkakunstmaanemailbundle_admin_messagesubscriber_edit', $children, $request);
            $this->addMenuForSubRoute($menu, $parent, 'Új feliratkozott', 'hgabkakunstmaanemailbundle_admin_messagesubscriber_add', $children, $request);
        }
    }

    protected function addMenuForSubRoute($menu, $parent, $label, $route, &$children, $request) {
        $menuItem = new MenuItem($menu);
        $menuItem->setUniqueId($route);
        $menuItem->setRoute($route);
        $menuItem->setLabel($label)->setAppearInNavigation(false)->setParent($parent);
        if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
            $menuItem->setActive(true);
        }

        $children[] = $menuItem;
    }

}
