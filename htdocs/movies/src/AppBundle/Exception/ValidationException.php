<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValidationException
 *
 * @author sunilg
 */
class ValidationException extends HttpException {
    
    public function __construct(ConstraintViolationListInterface $validationErrors) {

        $errorMessages = [];

        /* @var $error ConstraintViolationInterface */        
        foreach ($validationErrors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
                
        parent::__construct(400, json_encode($errorMessages));
    }
}
