<?php

namespace App\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: "/", name: "app_home")]
    public function home(): Response
    {
        return $this->render("home.html.twig", [
            "menu" => "home",
        ]);
    }
}
