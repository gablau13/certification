<?php

namespace App\Controller;



use App\Entity\Marque;
use DateTimeImmutable;
use App\Entity\Annonces;
use App\Form\ImagesType;
use App\Form\MarqueType;
use App\Entity\Categorie;


use App\Form\AnnonceType;
use App\Form\CategorieType;
use App\Entity\Commentaires;
use App\Entity\Images;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class AnnonceController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    
    

    #[Route('/annonces', name: 'app_annonce')]
    public function index(): Response
    {

        // Afficher tous les annonces
        $annonce = $this->getDoctrine()->getRepository(Annonces::class)->findAll();
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonce,
        ]);
    }

    #[Route('/annonces/create', name: 'annonces_create')]
    public function create(Request $request): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Récupération de l'image depuis le formulaire
            $coverImage = $form->get('coverImage')->getData();
            if ($coverImage) {
                //création d'un nom pour l'image avec l'extension récupérée
                $imageName = md5(uniqid()) . '.' . $coverImage->guessExtension();

                //on déplace l'image dans le répertoire cover_image_directory avec le nom qu'on a crée
                $coverImage->move(
                    $this->getParameter('cover_image_directory'),
                    $imageName
                );

                // on enregistre le nom de l'image dans la base de données
                $annonce->setCoverImage($imageName);
            }
            
            $this->manager->persist($annonce);
            $this->manager->flush();

            return $this->redirectToRoute('app_annonce');
        }

        return $this->render('annonce/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/annonces/marque', name: 'annonces_marque')]
    public function createMarque(Request $request): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 

            $this->manager->persist($marque);
            $this->manager->flush();

            return $this->redirectToRoute('annonces_categorie');
   
       }
       return $this->render('annonce/Marque.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/annonces/categorie', name: 'annonces_categorie')]
    public function createCategorie(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
           
            $this->manager->persist($categorie);
            $this->manager->flush();

            return $this->redirectToRoute('annonces_create');
   
       }
       return $this->render('annonce/Marque.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/annonces/{slug}', name: 'annonces_show')]
    
    public function Show(Annonces $annonce, Request $request, EntityManagerInterface $manager): Response
    {
        $annonce = $this->getDoctrine()->getRepository(Annonces::class)->find($annonce);
        
        $commentaire = new Commentaires;
        // On génére le formulaire
        $commentaireForm = $this->CreateForm(CommentaireType::class, $commentaire);
        $commentaireForm->handleRequest($request);

        //Traitement du formulaire
        if($commentaireForm->isSubmitted() && $commentaireForm->isValid())
        {
            $commentaire->setCreatedAt(new DateTimeImmutable());
            $commentaire->setAnnonces($annonce);
            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute('annonces_show', ['slug'=>$annonce->getSlug()]);
        }

        return $this->render('annonce/show.html.twig', [
            'annonces' => $annonce,
            
            'commentaireForm' => $commentaireForm->createView()
        ]);
}

#[Route('/annonce/show/image/{id}', name: 'annonces_show_image')]
    
    public function createImage(Images $image, Request $request, EntityManagerInterface $manager): Response
    {
        $image = $this->getDoctrine()->getRepository(Images::class)->find($image);
        
        $image = new Images;
        // On génére le formulaire
        $imageForm = $this->CreateForm(ImagesType::class, $image);
        $imageForm->handleRequest($request);

        //Traitement du formulaire
        if($imageForm->isSubmitted() && $imageForm->isValid())
        {
           
           
            $manager->persist($image);
            $manager->flush();

           
        }

        return $this->render('annonce/createimages.html.twig', [
           
            
            'ImageForm' => $imageForm->createView()
        ]);
}
}