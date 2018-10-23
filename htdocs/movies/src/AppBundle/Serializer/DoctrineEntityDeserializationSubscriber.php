<?php

namespace AppBundle\Serializer;

use AppBundle\Annotation\DeserializeEntity;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use LogicException;
use ReflectionClass;
use ReflectionObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function dump;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface {

    /**
     * @var Registry
     */
    private $doctrineRegistry;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader, Registry $doctrineRegistry) {
        
        $this->annotationReader = $annotationReader;
        $this->doctrineRegistry = $doctrineRegistry;
    }
    
    public static function getSubscribedEvents() {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method'=> 'onPreDeserialize',
                'format' => 'json'
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json'
            ]
        ];
    }
    
    public function onPreDeserialize(PreDeserializeEvent $event) {
        $deserializedClassName = $event->getType()['name'];
        
        if (!class_exists($deserializedClassName)) {
            return;
        }
        
        $data = $event->getData();
        
        //dump($deserializedClassName, $data);
        
        $class = new ReflectionClass($deserializedClassName);
        
        foreach ($class->getProperties() as $property) {
            if (!isset($data[$property->name])) {
                continue;
            }
            
            /**
             * @var DeserializeEntity $annotation
             */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, DeserializeEntity::class);
            
            if (null === $annotation || !class_exists($annotation->className) ) {
                continue;
            }
            
            $data[$property->name] = [
                $annotation->idFieldName => $data[$property->name]
            ]; // This assosciative array value for the person field in MovieRole
               // JSON body data makes it recognizable to deserializer as Person entity id.
            
            $event->setData($data);
        } //foreach        
    }
    
    /* Note: When adding movie role, this gets called twice, once for Person and another time for MovieRole*/
    public function onPostDeserialize(ObjectEvent $event) {

        $deserializedClassName = $event->getType()['name'];
        
        if (!class_exists($deserializedClassName)) {
            return;   
        }
        
        $deserializedObject = $event->getObject();
        
        //dump("Deserialized Object\n", $deserializedObject);
        
        $reflection = new ReflectionObject($deserializedObject);

        //dump("Reflection\n", $reflection);
        
        foreach ($reflection->getProperties() as $property) {
            //dump("Property\n", $property);
            /** @var DeserializeEntity $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );
            
            //dump("Annotaton\n", $annotation);
            
            if (null === $annotation || !class_exists($annotation->className)) {
                continue;
            }
            
            if (!$reflection->hasMethod($annotation->setterMethodName)) {
                throw new LogicException(
                    "Object {$reflection->getName()} does not have the {$annotation->setterMethodName} method."
                );
            }
            
            $property->setAccessible(true); // Before you can directly read or update a property value, it needs to be made accessible. 
            // This is the MovieRole object. Get the person object from its person property.
            $deserializedEntity = $property->getValue($deserializedObject);
            
            //dump("Deserialized entity\n", $deserializedEntity);
            
            if (null === $deserializedEntity) {
                return;
            }
            
            $entityId = $deserializedEntity->{$annotation->idGetterMethodName}();
            
            //dump("Deserialized Entity Id", $entityId);
            
            $repository = $this->doctrineRegistry->getRepository($annotation->className);
            
            //dump("Repository", $repository);
            
            $entity = $repository->find($entityId);
            
            //dump("Doctrine returned entity", $entity);
            
            if (null === $entity) {
                throw new NotFoundHttpException(
                    "{$reflection->getShortName()}/$entityId"
                );
            }
            
            $deserializedObject->{$annotation->setterMethodName}($entity);
        }
    }
}
