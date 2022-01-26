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

                $profilePictureFile = $form->get('imageFile')->getData();

                // Check if any profile picture as been uploaded
                if($profilePictureFile) {

                    $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();

                    // Move the file to the directory where images are stored
                    try {
                        $profilePictureFile->move(
                            $this->getParameter('profile_pictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $error = "Une erreur est survenue durant le transfert de votre photo de profil.";
                    }

                    // updates the 'imageFile' property to store the profile picture name
                    // instead of its contents
                    $user->setImageFile($newFilename);

                }

                if(!$error) {
                    $entityManager->flush();

                    return $this->redirectToRoute('show_profile', [
                        'username' => $user->getNickName()
                    ]);
                }
            }

            return $this->render('profile/edit.html.twig', [
                'form' => $form->createView(),
                'error' => isset($error) ? $error : null,
            ]);
        }

        return $this->redirectToRoute('show_profile', ['username' => $username]);
    }
}
