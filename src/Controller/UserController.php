<?php

namespace App\Controller;

use id;
use App\Entity\Users;
use App\Entity\Images;
use App\Entity\Annonces;
use App\Form\EditUserType;
use App\Form\EditAnnonceUserType;
use App\Repository\UsersRepository;
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
        $user =$usersRepository ->find($user);
        return $this->render('user/user.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profil/annonce', name: 'profil_user_annonce')]
    public function annonceUser(): Response
    {
       
        $user = $this->getUser();
        $annonce = $this->getDoctrine()->getRepository(Annonces::class)->find($user);
        
        
        return $this->render('user/annonceuser.html.twig', [
            'annonce' => $annonce,
            
            
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


}
