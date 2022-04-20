<?php

namespace App\Http\Controller\Auth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class SocialLoginController extends AbstractController
{
    private const SCOPES = [
        "github" => ["user:email"],
        "google" => ["profile", "email", "openid"],
    ];

    public function __construct(private readonly ClientRegistry $clientRegistry)
    {
    }

    #[Route(path: "/oauth/{service}/connexion", name: "oauth_connect")]
    public function connectAction(string $service): RedirectResponse {
        $this->ensureServiceAccepted($service);

        return $this->clientRegistry
            ->getClient($service)
            ->redirect(self::SCOPES[$service]);
    }

    #[Route(path: "/oauth/{service}/check", name: "oauth_check")]
    public function connectCheckAction()
    {
        // Méthode qui doit être nulle pour effectuer l'auth dans GoogleAuthenticator
    }

    private function ensureServiceAccepted(string $service): void
    {
        if (!in_array($service, array_keys(self::SCOPES))) {
            throw new AccessDeniedException();
        }
    }
}
