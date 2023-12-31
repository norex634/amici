<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\ClasseRepository;
use App\Repository\SpeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'Register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ClasseRepository $classeRepo, SpeRepository $speRepo): Response
    {

        $classe = $classeRepo->findAll();
        $spe = $speRepo->findAll();
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('HomePage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'classes' => $classe,
            'spes' => $spe,
        ]);

    }

}
