<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Id;
use InvalidArgumentException;
use ReflectionObject;
use ReflectionProperty;

/**
 * Description of EntityMerge
 *
 * @author sunilg
 */
class EntityMerger {

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader) {
        $this->annotationReader = $annotationReader;
    }
    
    public function merge($entity, $changes): void {
        $entityClassName = get_class($entity);
        if (false === $entityClassName) {
            throw new InvalidArgumentException('$entity is not a class');
        }
        
        $changesClassName = get_class($changes); // get_class returns boolean false when not a class
        if (false === $changesClassName) {
            throw new InvalidArgumentException('$changes is not a class');
        }
        
        if (!is_a($changes, $entityClassName)) {
            throw new InvalidArgumentException("Cannot merge object of class $changesClassName with object of class $entityClassName");
        }
        
        $entityReflection = new ReflectionObject($entity);
        $changesReflection = new ReflectionObject($changes);
        
        
        /* @var $changedProperty ReflectionProperty  */
        foreach ($changesReflection->getProperties() as $changedProperty) {
            $changedProperty->setAccessible(true);
            
            $changedPropertyName = $changedProperty->getName();
            $changedPropertyValue = $changedProperty->getValue($changes);
            
            if ((false === $entityReflection->hasProperty($changedPropertyName)) || (null === $changedPropertyValue)) {
                continue;
            }
            
            /* @var $entityProperty ReflectionProperty  */
            $entityProperty = $entityReflection->getProperty($changedPropertyName);
            
            // Id field cannot be updated
            if ($this->annotationReader->getPropertyAnnotation($entityProperty, Id::class) !== null) {
                continue;
            }
            
            $entityProperty->setAccessible(true);
            $entityProperty->setValue($entity, $changedPropertyValue);
        }
    }
}
