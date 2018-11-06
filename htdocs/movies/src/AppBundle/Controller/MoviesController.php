<?php

namespace AppBundle\Controller;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use FOS\RestBundle\Controller\ControllerTrait;


use AppBundle\Entity\EntityMerger;
use AppBundle\Entity\Movie;
use AppBundle\Entity\MovieRole;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * By default give access to controllers for all users: 
 * Do we need this? -- <ReplaceWithAtSymbol>Security("is_anonymous() or is_authenticated()")
 */
class MoviesController extends AbstractController {

    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;
    
    /**
     * 
     * @param EntityMerger $entityMerger
     */
    function __construct(EntityMerger $entityMerger) {
        
        $this->entityMerger = $entityMerger;
    }
    
    /**
     * @Rest\View()
     */
    public function getMoviesAction() {
        $movies  = $this->getDoctrine()->getRepository('AppBundle:Movie')->findAll();
        return $movies;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     * @Security("is_authenticated()");
     */
    public function postMoviesAction(Movie $movie, ConstraintViolationListInterface $validationErrors) {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);  
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();
        
        return $movie;
    }
    
    /**
     * @Rest\View()
     * @Security("is_authenticated()");
     */
    public function deleteMoviesAction(?Movie $movie) {
        if (NULL === $movie) {
            return $this->view(null, 404);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();
    }
    
    /**
     * @Rest\View()
     */
    public function getMovieAction(?Movie $movie) {
        if (NULL === $movie) {
            return $this->view(null, 404);
        }
        
        return $movie;
    }

    /**
     * @Rest\View()
     */    
    public function getMovieRolesAction(?Movie $movie) {
        if (NULL === $movie) {
            return $this->$view(null, 400);
        }
        
        return $movie->getRoles();
    }
    

    /**
     * @Rest\View()
     * @ParamConverter("role", converter="fos_rest.request_body", options={"deserializationContext" = {"groups" = {"Deserialize"}}})
     * @Rest\NoRoute()
     * @Security("is_authenticated()");
     */
    public function postMovieRolesAction(Movie $movie, MovieRole $role, ConstraintViolationListInterface $validationErrors) {
        
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);  
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $role->setMovie($movie);
        $em->persist($role);
        
        $movie->getRoles()->add($role);
        $em->persist($movie);
        
        $em->flush();
        
        return $role;
    }
    
    /**
     * @Rest\View()
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body", options={"validator" = {"groups" = {"Patch"}}})
     * @Security("is_authenticated()");
     */
    public function patchMovieAction(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors) {
        if (null === $movie) {
            return $this->view(null, 404);
        }
        
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);  
        }
        
        //Merge entities
        $this->entityMerger->merge($movie, $modifiedMovie);

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();
        
        return $movie;
    }
    
}
