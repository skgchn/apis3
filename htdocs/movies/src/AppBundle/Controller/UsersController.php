<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EntityMerger;
use AppBundle\Entity\Movie;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Is Security annotation needed here, because ANONYMOUS and AUTHENTICATED is everybody?
 * @Security("is_anonymous() or is_authenticated()")
 */
class UsersController extends AbstractController {

    /**
     * @var EntityMerger
     */
    private $entityMerger;

    use ControllerTrait;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder, EntityMerger $entityMerger) {
        
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->entityMerger = $entityMerger;
    }
    
    /**
     * @Rest\View()
     * @Security("is_granted('show', theUser)", message="Access denied") // currently logged in user provided as $user, so using $theUser as param
     */
    public function getUserAction(?User $theUser) {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }
        
        return $theUser;
    }
    
    /**
     * @Rest\View(statusCode=201)
     * @Rest\NoRoute()
     * @ParamConverter("user", converter="fos_rest.request_body",
     *      options={"deserializationContext"={"groups"={"Deserialize"}}})
     */
    public function postUserAction(User $user, ConstraintViolationListInterface $validationErrors) {
        if (count($validationErrors)) {
            throw new ValidationException($validationErrors);
        }

        $this->encodePassword($user);        
        $user->setRoles([User::ROLE_USER]);
        
        $this->persistUser($user);
        
        return $user;
    }
    
    /**
     * @Rest\View()
     * @ParamConverter("modifiedUser", converter="fos_rest.request_body",
     *       options={
     *            "validator" = {"groups" = {"Patch"}},
     *            "deserializationContext" = {"groups"={"Patch"}}
     *       }
     * )
     * @Security("is_granted('edit', theUser)", message="Access denied")
     */
    public function patchUserAction(?User $theUser, User $modifiedUser, ConstraintViolationListInterface $validationErrors) {
        if (null === $theUser) {
            #return $this->view(null, 404);
            throw new NotFoundHttpException();
        }
        
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);  
        }
        
/*
        // Since Assert\Expression whenPresentIsNotEmpty() is added to the password field in User.php, the below check for empty password is not needed
        if (empty($modifiedUser->getPassword())) {
            $modifiedUser->setPassword(null);
        }
 */
        
        //Merge entities
        $this->entityMerger->merge($theUser, $modifiedUser);

        $this->encodePassword($theUser);

        $this->persistUser($theUser);
        
        return $theUser;
    }
    
    /**
     * 
     * @param User $user
     * @return void
     */
    protected function encodePassword(User $user) : void {
        $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPassword())
               );
    }
    
    /**
     * 
     * @param User $user
     * @return void
     */
    protected function persistUser(User $user) : void {
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();        
    }
}

