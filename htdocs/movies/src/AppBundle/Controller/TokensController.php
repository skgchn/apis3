<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder) {
        
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }
    
    /**
     * @Rest\View(statusCode = 201)
     */
    
    public function postTokenAction(Request $request) {

        /* @var $user User */
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findOneBy(['username' => $request->getUser()]);
        
        if (!$user) {
            throw new BadCredentialsException();
        }
        
        $passwordIsValid = $this->passwordEncoder->isPasswordValid($user, $request->getPassword());
        if (!$passwordIsValid) {
            throw new BadCredentialsException();
        }
        
        $token = $this->jwtEncoder->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600 // Should be getting jwt_token_ttl from parameters.yml instead of hardcoding
                                   // see https://stackoverflow.com/questions/13901256/how-do-i-read-from-parameters-yml-in-a-controller-in-symfony2
        ]);
        
        return new JsonResponse(['token' => $token]);        
    }
}
