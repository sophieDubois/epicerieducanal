<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    /**
     * Controleur de la page d'accueil
     */
    #[Route('/', name: 'main_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }


    /*Controleur de la page nos produits*/
    #[Route('/nos-produits/', name: 'main_product')]
    public function product(): Response
    {
        return $this->render('main/product.html.twig');
    }


    /*Controleur de la page tourisme*/
    #[Route('/tourisme/', name: 'main_tourism')]
    public function tourism(): Response
    {
        return $this->render('main/tourism.html.twig');
    }

    /*Controleur de la page contact*/
    #[Route('/contact/', name: 'main_contact')]
    public function contact(): Response
    {
        return $this->render('main/contact.html.twig');
    }

    /*Controleur de la page de profil
    acces reservé aux personnes connectées (ROLE_USER*/
    #[Route('/mon-profil/', name: 'main_profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }


}




