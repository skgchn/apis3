<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Security\TokenStorage;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Is Security annotation needed here, because ANONYMOUS and AUTHENTICATED is everybody?
 * @Security("is_anonymous() or is_authenticated()")
 */
class TokensController  extends AbstractController {

    use ControllerTrait;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
            UserPasswordEncoderInterface $passwordEncoder,
                JWTEncoderInterface $jwtEncoder,
                    TokenStorage $tokenStorage,
                        LoggerInterface $logger) {
        
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }
    
    /**
     * @Rest\View(statusCode = 201)
     */
    
    public function postTokenAction(Request $request) {

        $this->logger->debug("Generating token for user " . $request->getUser());

        /* @var $user User */
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findOneBy(['username' => $request->getUser()]);
        
        if (!$user) {
            $this->logger->debug("User " . $request->getUser() . " not present");
            throw new BadCredentialsException();
        }
        
        $passwordIsValid = $this->passwordEncoder->isPasswordValid($user, $request->getPassword());
        if (!$passwordIsValid) {
            $this->logger->debug("Incorrect password for user " . $request->getUser());
            throw new BadCredentialsException();
        }
        
        $token = $this->jwtEncoder->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600 // Should be getting jwt_token_ttl from parameters.yml instead of hardcoding
                                   // see https://stackoverflow.com/questions/13901256/how-do-i-read-from-parameters-yml-in-a-controller-in-symfony2
        ]);
        
        $this->tokenStorage->storeToken($user->getUsername(), $token);

        return new JsonResponse(['token' => $token]);
    }
}
