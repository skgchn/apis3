<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PeopleController extends AbstractController {

    use ControllerTrait;
    
    /**
     * @Rest\View()
     */
    public function getPeopleAction() {
        $persons  = $this->getDoctrine()->getRepository('AppBundle:Person')->findAll();
        return $persons;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("person", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     */
    public function postPeopleAction(Person $person, ConstraintViolationListInterface $validationErrors) {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($person);
        $em->flush();
        
        return $person;
    }
    
    /**
     * @Rest\View()
     */
    public function deletePeopleAction(?Person $person) {
        if (NULL === $person) {
            return $this->view(null, 404);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();
    }
    
    /**
     * @Rest\View()
     */
    public function getPersonAction(?Person $person) {
        if (NULL === $person) {
            return $this->view(null, 404);
        }
        
        return $person;
    }
    
    //Rest\Get("persons/{person}")  // Symfony otherwise names the route as people which is plural of person
}
