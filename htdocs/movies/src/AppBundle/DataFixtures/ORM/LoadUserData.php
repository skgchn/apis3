<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture as Fixture;
use Doctrine\Common\Persistence\ObjectManager as ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUserData extends Fixture implements ContainerAwareInterface {

    /**
     *
     * @var ContainerInterface;
     */
    private $container;

    public function load(ObjectManager $manager) {
        
        /* @var $passwordEncoder UserPasswordEncoderInterface */
        $passwordEncoder = $this->container->get('security.password_encoder');
        $user1 = new User();
        $user1->setUsername('user1');
        $user1->setPassword($passwordEncoder->encodePassword($user1, 'Secure123!'));
        $user1->setRoles([User::ROLE_ADMIN]);
        $manager->persist($user1);
        
        $user2 = new User();
        $user2->setUsername('user2');
        $user2->setPassword($passwordEncoder->encodePassword($user2, 'Secure123!'));
        $user2->setRoles([User::ROLE_ADMIN]);
        $manager->persist($user2);
        
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}
