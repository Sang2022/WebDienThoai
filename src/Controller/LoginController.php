<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    /* public function index(): Response
     {
         return $this->render('login/index.html.twig', [
             'controller_name' => 'LoginController',
         ]);
     }
    */
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, ManagerRegistry $doctrine): Response{
        // get the login error if there is one
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user q
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'categories'    => $categories

        ]);


    }
}
