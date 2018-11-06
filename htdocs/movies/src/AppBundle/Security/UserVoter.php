<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Security;

use AppBundle\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter{

    const SHOW = 'show';
    const EDIT = 'edit';
    
    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;
    
    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        
        $this->decisionManager = $decisionManager;
    }
    
    protected function supports($attribute, $subject): bool {
        if (!in_array($attribute, [self::SHOW, self::EDIT])) {
            return false;
        }
        
        if (!$subject instanceof User) {
            return false;
        }
        
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        // The user must be authenticated to see user details
        /* @var $authenticatedUser User */
        $authenticatedUser = $token->getUser();
        if (!$authenticatedUser instanceof User) {
            return false;
        }
        
        /* @var $theUser User */
        $theUser = $subject;
        
        switch ($attribute) {
            case self::SHOW:
            case self::EDIT:
                if ($this->decisionManager->decide($token, [User::ROLE_ADMIN])) {
                    return true;
                }
                
                return $authenticatedUser->getId() === $theUser->getId();
                break;
        }
        
        throw new LogicException('This code should not be reached!');
    }
}
