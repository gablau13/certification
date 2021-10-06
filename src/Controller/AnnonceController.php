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
    #[Route('/annonces/categorie', name: 'app_annonce_categorie')]
    public function createCategorie(Request $request, EntityManagerInterface $manager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            $this->manager = $manager;
            $manager->persist($categorie);
            $manager->flush();
        }
        return $this->render('annonce/Categorie.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/annonces/marque', name: 'app_annonce_marque')]
    public function createMarque(Request $request, EntityManagerInterface $manager): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            $this->manager = $manager;
            $manager->persist($marque);
            $manager->flush();
        }
        return $this->render('annonce/Categorie.html.twig', [
            'categorie' => $marque,
            'form' => $form->createView()
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
            //Récupération des images transmises
            $images = $form->get('images')->getData();
            //on boucle sur les images
            foreach ($images as $image) {
                // on génére un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                // on copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('image_directory'),
                    $fichier
                );
                // On stocke l'image dans la base données
                $img = new Images();
                $img->setNomUrl($fichier);
                $annonce->addImage($img);
            }
            $annonce->setUsers($this->getUser());
            $this->manager->persist($annonce);
            $this->manager->flush();

            return $this->redirectToRoute('app_annonce');
        }

        return $this->render('annonce/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }




    #[Route('/annonces/{slug}', name: 'annonces_show')]

    public function Show(Annonces $annonce, Request $request, EntityManagerInterface $manager): Response
    {



        $annonce = $this->getDoctrine()->getRepository(Annonces::class)->find($annonce);
        $commentaire = new Commentaires();
        // On génére le formulaire
        $commentaireForm = $this->CreateForm(CommentaireType::class, $commentaire);
        $commentaireForm->handleRequest($request);

        //Traitement du formulaire
        if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
            $commentaire->setCreatedAt(new DateTimeImmutable());
            $commentaire->setAnnonces($annonce);
            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute('annonces_show', ['slug' => $annonce->getSlug()]);
        }

        return $this->render('annonce/show.html.twig', [
            'annonces' => $annonce,

            'commentaireForm' => $commentaireForm->createView()
        ]);
    }

    #[Route('/annonce/image/{slug}', name: 'annonces_show_image')]

    public function createImage(Annonces $annonce, Request $request, EntityManagerInterface $manager): Response
    {
        $image = $this->getDoctrine()->getRepository(Images::class)->find($annonce);

        $imageForm = new Images();
        // On génére le formulaire
        $imageform = $this->CreateForm(ImagesType::class, $image);
        $imageform->handleRequest($request);
        //Traitement du formulaire
        if ($imageform->isSubmitted() && $imageform->isValid()) {
            // Récupération de l'image depuis le formulaire
            $image = $imageform->get('Images')->getData();
            $image->setAnnonces($annonce);
            $manager->persist($image);
            $manager->flush();

            return $this->redirectToRoute('annonces_show_image', ['slug' => $annonce->getSlug()]);
        }
        return $this->render('annonce/createimages.html.twig', [
            'annonces' => $annonce,

            'imageForm' => $imageform->createView()
        ]);
    }

    #[Route('/annonces/edit/{slug}', name: 'annonces_edit')]
    public function edit(Request $request, Annonces $annonce): Response
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
            //Récupération des images transmises
            $images = $form->get('images')->getData();
            //on boucle sur les images
            foreach ($images as $image) {
                // on génére un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                // on copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('image_directory'),
                    $fichier
                );
                // On stocke l'image dans la base données
                $img = new Images();
                $img->setNomUrl($fichier);
                $annonce->addImage($img);
            }

            $this->manager->persist($annonce);
            $this->manager->flush();
            $this->addFlash('success', 'Annonce modifier avec succès!');
            return $this->redirectToRoute('app_annonce');
        }

        return $this->render('annonce/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   
}
