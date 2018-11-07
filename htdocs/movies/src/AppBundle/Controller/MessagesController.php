<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use AppBundle\Service\MessageGenerator;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Description of MessagesController
 *
 * @author sunilg
 */
class MessagesController extends AbstractController {
    
    use ControllerTrait;
    
    /**
     * @Rest\View()
     * @Rest\NoRoute()
     */
    public function getMessageAction(MessageGenerator $messageGenerator) {
        $message = $messageGenerator->getHappyMessage();

        return $message;
    }
}
