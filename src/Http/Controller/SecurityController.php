<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Http\Form\User\UserForm;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class SecurityController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route(path: "/connexion", name: "app_login")]
    public function login(): Response
    {
        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute("app_home");
        }

        return $this->render("security/login.html.twig");
    }

    #[Route(path: "/deconnexion", name: "app_logout")]
    public function logout(): void
    {
        throw new LogicException(
            "This method can be blank - it will be intercepted by the logout key on your firewall."
        );
    }

    #[Route(path: "/inscription", name: "app_signup")]
    public function signUp(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder
    ): RedirectResponse|Response {
        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute("app_home");
        }
        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setPassword(
                    $passwordEncoder->hashPassword($user, $user->getPassword())
                );
                $this->em->persist($user);
                $this->em->flush();
                return $this->redirectToRoute("app_login");
            } catch (UniqueConstraintViolationException) {
                $this->addFlash(
                    "warning",
                    "Nom d'utilisateur ou adresse mail déjà utilisé"
                );
            }
        }
        return $this->render("security/signup.html.twig", [
            "form" => $form->createView(),
        ]);
    }
}
