<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture as Fixture;
use Doctrine\Common\Persistence\ObjectManager as ObjectManager;

class LoadPersonData extends Fixture {
    public function load(ObjectManager $manager) {
        $person1 = new Person();
        $person1->setFirstName('Gary');
        $person1->setLastName('Smith');
        $person1->setDateOfBirth(new \DateTime('1958-10-17'));
        
        $manager->persist($person1);
        $manager->flush();
    }
}