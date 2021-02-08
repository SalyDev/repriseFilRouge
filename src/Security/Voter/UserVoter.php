<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    private $security;
    function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'VIEW_ALL', 'ADD', "VIEW_ADMIN"])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return (($user == $subject || $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_CM')));
                break;
            case 'VIEW_ALL':
                // logic to determine if the user can VIEW
                // return true or false
                return (($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_CM') || $this->security->isGranted('ROLE_FORMATEUR')));
                break;
            case 'ADD':
                // logic to determine if the user can VIEW
                // return true or false
                return ($this->security->isGranted('ROLE_ADMIN'));
                break;
            case 'VIEW_ADMIN':
                // logic to determine if the user can VIEW
                // return true or false
                return ($user == $subject);
                break;
        }

        return false;
    }
}
