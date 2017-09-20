<?php

namespace Hgabka\KunstmaanEmailBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EmailFixtures
 *
 */
class EmailFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container = null;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createAttachmentFolder();
    }

    /**
     * Create some dummy media files
     */
    private function createAttachmentFolder()
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        // Add images to database
        $folderRepo = $this->manager->getRepository('KunstmaanMediaBundle:Folder');
        $fileFolder = $folderRepo->findOneBy(['rel' => 'files']);

        $attFolder = $folderRepo->findOneByInternalName('attachment');
        if (!$attFolder) {
            $attFolder = new Folder();
            $attFolder
                ->setParent($fileFolder)
                ->setInternalName('attachment')
                ->setName($translator->trans('hgabka_kuma_email.fixtures.folder_name', [], null, $this->container->getParameter('defaultlocale')))
                ->setRel('attachment')
            ;
            $this->manager->persist($attFolder);
            $this->manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 52;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
