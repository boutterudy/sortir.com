<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil/{username}", name="show_profile")
     */
    public function show(UserRepository $userRepository, string $username): Response
    {
        // Get User entity
        $user = $userRepository->findOneByNickName($username);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profil/{username}/edit", name="edit_profile")
     */
    public function edit(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository, string $username): Response
    {
        // Get User entity
        $user = $userRepository->findOneByNickName($username);
        $loggedUser = $this->getUser();

        if($user === $loggedUser) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                // Hash password before flush
                $user->setPassword($passwordHasher->hashPassword(
                   $user,
                   $user->getPassword()
                ));

                $entityManager->flush();

                return $this->redirectToRoute('show_profile', [
                    'username' => $user->getNickName()
                ]);
            }

            return $this->renderForm('profile/edit.html.twig', [
                'form' => $form,
            ]);
        }

        return $this->redirectToRoute('show_profile', ['username' => $username]);
    }
}
