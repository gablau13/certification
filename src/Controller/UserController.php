<?php

namespace App\Controller;

use id;
use App\Entity\Users;
use App\Entity\Images;
use App\Entity\Annonces;
use App\Form\AnnonceType;
use App\Form\EditUserType;
use App\Form\EditAnnonceUserType;
use App\Repository\UsersRepository;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'profil_user')]
    public function index(UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();
        // dd($user);
        $user = $usersRepository->find($user);
        return $this->render('user/user.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profil/annonce', name: 'profil_user_annonce')]
    public function annonceUser(AnnoncesRepository $annoncesRepository): Response
    {

        $user = $this->getUser('id');
        $annonces = $annoncesRepository->findAnnoncesUser($user);
        return $this->render('user/annonceuser.html.twig', [
            'annonces' => $annonces,


        ]);
    }



    #[Route('/Profil/edit/{id}', name: 'profil_edit')]
    public function editProfil(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {




            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'profil modifier avec succès!');
            return $this->redirectToRoute('profil_user');
        }

        return $this->render('user/edituser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('profil/annonces/edit/{slug}', name: 'profil_annonces_edit')]
    public function editAnnonce(Request $request, Annonces $annonce): Response
    {
       
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
           
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($annonce);
            $manager->flush();
            $this->addFlash('success', 'Annonce modifier avec succès!');
            return $this->redirectToRoute('profil_user_annonce');
        }

        return $this->render('user/editannonceuser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
