<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgottenPasswordType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/connexion", name="login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if(!$this->getUser() instanceof User) {
            // Get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // Last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('login/index.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }
        return $this->redirectToRoute('temporary');
    }

    /**
     * @Route ("/oubli-mot-de-passe", name="forgotten_password")
     */
    public function recoverPassword(MailerInterface $mailer,
                                    UserRepository $userRepository,
                                    Request $request,
                                    TokenGeneratorInterface $tokenGenerator,
                                    EntityManagerInterface $entityManager
    ){

        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $emailRequested = $data['email'];
            $currentUser = $userRepository->findOneBy(['email'=>$emailRequested]);
            if(!$currentUser)
            {
                $this->addFlash('success', 'Un lien de réinitialisation du mot de passe vous a été envoyé ! (Vérifiez votre dossier spam/courrier indésirable)');
                return $this->redirectToRoute('login');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $currentUser->setResetToken($token);
                $entityManager->persist($currentUser);
                $entityManager->flush();
            }catch (\Exception $exception){
                $this->addFlash('warning', $exception->getMessage());
                return $this->redirectToRoute('login');
            }

            $reinitUrl = $this->generateUrl('reset_password', array('token'=>$token),
                UrlGeneratorInterface::ABSOLUTE_URL);

            $mailer = new MailerService($mailer);
            $mailer->sendEmail('mdp.oublie.sortir.com@gmx.fr', $currentUser->getEmail(), 'Votre lien de réinitialisation de mot de passe', 'Vous avez demandé à réinitialiser votre mot de passe sur Sortir.com, voici le lien de réinitialisation : ' . $reinitUrl);
            $this->addFlash('success', 'Un lien de réinitialisation du mot de passe vous a été envoyé ! (Vérifiez votre dossier spam/courrier indésirable)');
            return $this->redirectToRoute('login');
        }
        return $this->render('login/forgotten_password.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $token
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UserRepository $userRepository
     * @return Response
     * @Route ("/reinitialiser-mot-de-passe/{token}", name="reset_password")
     */
    public function resetPassword(Request $request,
                                  string $token,
                                  UserPasswordHasherInterface $passwordHasher,
                                  UserRepository $userRepository,
                                    EntityManagerInterface $entityManager){
        $user = $userRepository->findOneBy(['reset_token'=>$token]);
        if (!$user){
            $this->addFlash('danger', 'Une erreur s\'est produite');
            return $this->redirectToRoute('login');
        }

        if($request->isMethod('POST')){
            $user->setResetToken(null);
            $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('password')));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('login');
        }
        return $this->render('login/reset_password.html.twig',
                [
                    'token'=>$token
                ]);
    }
}
