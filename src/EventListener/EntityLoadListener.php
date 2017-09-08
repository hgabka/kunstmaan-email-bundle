<?php
/**
 * Created by PhpStorm.
 * User: sfhun
 * Date: 2017.09.08.
 * Time: 19:03
 */

namespace Hgabka\KunstmaanEmailBundle\EventListener;

use Hgabka\KunstmaanEmailBundle\Entity\EmailTemplateTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class EntityLoadListener
{
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $obj = $eventArgs->getEntity();
        $em = $eventArgs->getEntityManager();

        // @TODO: inkább valami service kéne, ami visszaadja az entitykhez a product documenteket
        if ($obj instanceof EmailTemplateTranslation) {

            $attachments = $em->getRepository('HgabkaKunstmaanEmailBundle:Attachment')->getByTemplate($obj->getTranslatable(),$obj->getLocale());
            $productReflProp = $em->getClassMetadata(get_class($obj))->reflClass->getProperty('attachments');
            $productReflProp->setAccessible(true);

            $collection = new ArrayCollection();
            foreach ($attachments as $att) {
                $collection->add($att);
            }

            $productReflProp->setValue($obj, $collection);

 /*           $productIds = $obj->getProductIds();

            if (!empty($productIds)) {
                $products = $dm->getRepository('UjhazPublicBundle:Product')
                               ->createQueryBuilder('p')
                               ->field('id')->in($productIds)
                               ->getQuery()
                               ->execute();

                $collection = new ArrayCollection();
                foreach ($products as $product) {
                    $collection->add($product);
                }

                $productReflProp = $em->getClassMetadata(get_class($obj))->reflClass->getProperty('products');
                $productReflProp->setAccessible(true);
                $productReflProp->setValue($obj, $collection);
            }*/
        }

    /*    if ($obj instanceof ProductCategoryProperty) {
            $productPropertyId = $obj->getProductPropertyId();

            if (!empty($productPropertyId)) {
                $productProperty = $dm->getRepository('UjhazPublicBundle:ProductProperty')->findOneBy(['id' => $productPropertyId]);
                $productReflProp = $em->getClassMetadata(get_class($obj))->reflClass->getProperty('productProperty');
                $productReflProp->setAccessible(true);
                $productReflProp->setValue($obj, $productProperty);
            }
        } */
    }
}