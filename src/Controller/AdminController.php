<?php

namespace App\Controller;


use App\Entity\Users;
use App\Entity\Images;
use App\Entity\Marque;
use App\Entity\Annonces;
use App\Form\MarqueType;
use App\Entity\Categorie;
use App\Form\AnnonceType;
use App\Form\AnnoncesType;
use App\Form\CategorieType;
use App\Entity\Commentaires;
use App\Repository\UsersRepository;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/', name: 'admin_index', methods: ['GET'])]
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }
    #[Route('/user', name: 'profils_user')]
    public function users(UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        $user = $usersRepository->findAll();
        return $this->render('admin/users.html.twig', [
            'user' => $user
        ]);
    }
    #[Route('/new', name: 'admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }
    #[Route('/new/categorie', name: 'app_annonce_categorie')]
    public function createCategorie(Request $request, EntityManagerInterface $manager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            $this->manager = $manager;
            $manager->persist($categorie);
            $manager->flush();
            return $this->redirectToRoute('admin_index');
        }
        return $this->render('admin/index.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView()
        ]);
    }
    #[Route('/new/marque', name: 'app_annonce_marque')]
    public function createMarque(Request $request, EntityManagerInterface $manager): Response
    {
        $marque = new Marque();
        $formmarque = $this->createForm(MarqueType::class, $marque);
        $formmarque->handleRequest($request);
        if ($formmarque->isSubmitted() && $formmarque->isValid()) {



            $this->manager = $manager;
            $manager->persist($marque);
            $manager->flush();
            return $this->redirectToRoute('admin_index');
        }
        return $this->render('admin/index.html.twig', [
            'marque' => $marque,
            'form' => $formmarque->createView()
        ]);
    }

    
    #[Route('/{id}', name: 'admin_show', methods: ['GET'])]
    public function show(Annonces $annonce): Response
    {
        return $this->render('admin/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
    
               
    #[Route('admin/annonce/delete/{id}', name: 'admin_annonce_delete')]
    public function delete(Annonces $annonce ): Response
    {

        $this->manager->remove($annonce);
        $this->manager->flush();
        $this->addFlash('success', 'Annonce supprimer avec succès!');
        return $this->redirectToRoute('admin_index');

    
    }

    #[Route('admin/user/delete/{id}', name: 'admin_user_delete')]
    public function deleteUser(Users $users ): Response
    {

        $this->manager->remove($users);
        $this->manager->flush();
        $this->addFlash('success', 'Utilisateur supprimer avec succès!');
        return $this->redirectToRoute('profil_user');

    
    }
   
}
