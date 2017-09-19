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

    /** @var  string */
    protected $editorRole;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, string $editorRole)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->editorRole = $editorRole;
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
            $menuItem->setLabel('Emailek');
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
            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('hgabkakunstmaanemailbundle_admin_emailtemplate')
                ->setUniqueId('email_template')
                ->setLabel('Email sablonok')
                ->setParent($parent);
            if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }

            $children[] = $menuItem;

            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('hgabkakunstmaanemailbundle_admin_emailtemplate')
                ->setUniqueId('email_template')
                ->setLabel('Email sablonok')
                ->setParent($parent)
                ->setAppearInNavigation(false);

            if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}
