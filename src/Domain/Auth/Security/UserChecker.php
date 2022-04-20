<?php

namespace App\Domain\Auth\Security;

use App\Domain\Auth\User;
use App\Domain\Auth\Exception\{UserBannedException, UserNotFoundException};
use Symfony\Component\Security\Core\User\{UserCheckerInterface, UserInterface};

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Vérifie que l'utilisateur a le droit de se connecter.
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
    }

    /**
     * Vérifie que l'utilisateur connecté a le droit de continuer.
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof User && $user->isBanned()) {
            throw new UserBannedException();
        }
        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        return;
    }
}
