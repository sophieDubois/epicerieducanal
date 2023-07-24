<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewPublicationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/*Préfixe de la route et du nom de toutes les pages de la partie blog du site*/
#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{

    /*Contôleur permettant de créer un nouvel article*/
    #[Route('/nouvelle-publication/', name: 'publication_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationNew(Request $request, ManagerRegistry $doctrine ): Response
    {
        //création d'un nouvelle article
        $newArticle = new Article();

        //creation d'un formulaire de creation d'article lié a l'article vide
        $form = $this->createForm(NewPublicationFormType::class, $newArticle);

        //liaison des données POST au formulaire
        $form->handleRequest($request);


        //si le formulaire a bien été envoyé et sans erreurs
        if ($form->isSubmitted() && $form->isValid()){

            //hydrater l'article
            $newArticle
                ->setPubicationDate(new \DateTime())
                ->setAuthor( $this->getUser() )
            ;

            //sauvegarde en base de données grace au manager des entités
            $em = $doctrine->getManager();
            $em->persist($newArticle);
            $em->flush();

            //message flash de succes
            $this->addFlash('success', 'Article publié avec succès !');

            //TODO: penser à rediriger sur la page qui montre le nouvel article
            return $this->redirectToRoute('main_home');

        }

        return $this->render('blog/publication_new.html.twig', [
            'new_publication_form' => $form->createView(),
        ]);
    }
}
