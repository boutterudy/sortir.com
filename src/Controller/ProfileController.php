<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function edit(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository, string $username): Response
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

                $newUsername = $user->getNickName();

                // Flush data
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('show_profile', [
                    'username' => $newUsername
                ]);
            }

            return $this->render('profile/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('show_profile', ['username' => $username]);
    }
}
