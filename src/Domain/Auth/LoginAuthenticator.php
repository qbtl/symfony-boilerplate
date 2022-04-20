<?php

namespace App\Domain\Auth;

use Exception;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\{
    Badge\CsrfTokenBadge,
    Badge\RememberMeBadge,
    Badge\UserBadge,
    Credentials\PasswordCredentials,
    Passport
};
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = "app_login";

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get("username", "");

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get("password", "")),
            [
                new CsrfTokenBadge(
                    "authenticate",
                    $request->request->get("_csrf_token")
                ),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        if (
            $targetPath = $this->getTargetPath(
                $request->getSession(),
                $firewallName
            )
        ) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate("app_home"));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
