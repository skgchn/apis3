<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class DeserializeEntity {
    /* Of source entity class of which the Id is passed to the annotation */
    /**
     * @var string
     * @Required
     */
    public $className;  // class name of the entity to fetch e.g. Person
    
    /**
     * @var string 
     * @Required
     */
    public $idFieldName; // id proporty name of this class - id
    
    /**
     * @var string
     * @Required
     */
    public $idGetterMethodName; // Getter method of the the id property of this class - getId()

    /* Of the class in which the annotation is used */
    /**
     * @var string
     * @Required 
     */
    public $setterMethodName; // e.g. setter for person property of MovieRole class 
}

