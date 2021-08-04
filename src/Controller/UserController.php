<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\EditUserType;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'profil_user')]
    public function index(): Response
    {
        $user = $this->getDoctrine()->getRepository(Users::class)->findAll();
        return $this->render('user/user.html.twig', [
            'user'=>$user
        ]);
    }

    #[Route('/Profil/edit/{id}', name: 'profil_edit')]
        public function editPrtofil(Request $request)
        {
           $user = $this ->getUser();
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