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

    /*Contrôleur permettant de créer un nouvel article*/
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
                ->setPublicationDate(new \DateTime())
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


    /*Contrôleur de la page listant tous les articles*/
    #[Route('/publications/liste/', name: 'publication_list')]
    public function publicationList(ManagerRegistry $doctrine): Response
    {
        //récupération du repository des articles
        $articleRepo = $doctrine->getRepository(Article::class);

        //on demande au repository de nous donner les articles qui sont en BDD
        $articles = $articleRepo->findAll();


        //on envoie les articles a la vue
        return $this->render('blog/publication_list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /*Contrôleur de la page d'édition des articles géré par ADMIN*/
    /*controleur de la page admin servant a modifier un article existant via son id passé ds l'URL
    acces reservé aux administrateur (ROLE_ADMIN)*/
    #[Route('/publication/modifier/{id}/', name: 'publication_edit', priority: 10)]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationEdit(Article $article, Request $request, ManagerRegistry $doctrine):Response
    {
        $form = $this->createForm(NewPublicationFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Publication modifiéé avec succès !');

            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $article->getSlug(),
            ]);
        }

        return $this->render('blog/publication_edit.html.twig', [
            'edit_form' => $form->createView(),
        ]);
    }

}




