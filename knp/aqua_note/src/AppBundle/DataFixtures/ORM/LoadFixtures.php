<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Genus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;


class LoadFixtures implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(__DIR__.'/fixtures.yml',
            ['providers' => [$this]])->getObjects();

        foreach($objectSet as $object) {
            $manager->persist($object);
        }
        $manager->flush();
    }

}