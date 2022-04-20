<?php

namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class GoogleAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    protected string $serviceName = "google";

    public const LOGIN_ROUTE = "app_login";

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return "oauth_check" === $request->attributes->get("_route") &&
            $request->get("service") === $this->serviceName;
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient($this->serviceName);
        $accessToken = $this->fetchAccessToken($client);
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use (
                $accessToken,
                $client
            ) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                return $this->userRepository->findOrCreateFromGoogleOauth(
                    $googleUser
                );
            })
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): RedirectResponse {
        $request->request->set("_remember_me", "1");

        return new RedirectResponse($this->urlGenerator->generate("app_home"));
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): RedirectResponse {
        if ($request->hasSession()) {
            $request
                ->getSession()
                ->set(Security::AUTHENTICATION_ERROR, $exception);
        }
        return new RedirectResponse(
            $this->urlGenerator->generate(self::LOGIN_ROUTE)
        );
    }
}
