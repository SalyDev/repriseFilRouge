<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupeVoter extends Voter
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
        return in_array($attribute, ['EDIT_VIEW'])
            && $subject instanceof \App\Entity\Groupe;
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
            case 'EDIT_VIEW':
                return $this->security->isGranted('ROLE_ADMIN');
                // logic to determine if the user can EDIT
                // return true or false
                break;
            // case 'VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                // break;
        }

        return false;
    }
}
