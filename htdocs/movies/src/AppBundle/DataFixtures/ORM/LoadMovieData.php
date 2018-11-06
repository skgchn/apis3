<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture as Fixture;
use Doctrine\Common\Persistence\ObjectManager as ObjectManager;

class LoadMovieData extends Fixture{
    public function load(ObjectManager $manager) {
        $movie1 = new Movie();
        $movie1->setTitle('Green Nile');
        $movie1->setYear(1999);
        $movie1->setTime(189);
        $movie1->setDescription('Just a movie description');
        
        $manager->persist($movie1);
        $manager->flush();
    }

}
