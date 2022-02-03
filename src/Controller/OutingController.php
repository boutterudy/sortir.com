<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Entity\User;
use App\Form\OutingCancelType;
use App\Form\OutingsFilterType;
use App\Form\OutingSubscriptionType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OutingController extends AbstractController
{

    /**
     * @Route("/", name="accueil")
     */
    public function home(OutingRepository $outingRepository, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(OutingsFilterType::class);
        $form->handleRequest($request);
        $subscribed = null;
        $unsubscribed = null;


        if ($form->isSubmitted() && $form->isValid())
        {
            $start = $form['startAt']->getData();
            $stop = $form['limitSubscriptionDate']->getData();

            $organizer = $form['organizer']->getData();

            $subscribed = $form['subscribed']->getData();
            $unsubscribed = $form['unsubscribed']->getData();

            $passed = $form['passed']->getData();
            $user = $userRepository->findOneById($this->getUser()->getId());
            $outingsList = $outingRepository->outingFilter($user, $organizer, $start, $stop, $passed);


        } else{
            $outingsList = $entityManager->getRepository(Outing::class)->findAll();

        }

        return $this->render('home.html.twig', [
            'unsubscribed'=>$unsubscribed,
            'subscribed'=>$subscribed,
            'outings'=> $outingsList,
            'outingForm'=>$form->createView()
        ]);

    }

    /**
     * @Route("/sortie/{id}/details", name="outing_details", requirements={"id"="\d+"})
     */
   public function display(OutingRepository $outingRepository, UserRepository $userRepository, int $id){
       $outing = $outingRepository->find($id);
       $user = $userRepository->findAll();
       return $this ->render('outing/display.html.twig',[
          'outing'=>$outing,
           'user'=>$user
       ]);
   }

    /**
     * @Route("/sortie/{id}/annuler", name="outing_cancel", requirements={"id"="\d+"})
     */
    public function index(int $id, EntityManagerInterface $entityManager, Request $request, OutingRepository $outingRepository, StatusRepository $statusRepository): Response
    {
        // Get Outing entity
        $outing = $outingRepository->findOneById($id);

        // Define URL to redirect
        $lastPage = $request->headers->get('referer');
        $urlToRedirect = $lastPage && $lastPage != $request->getUri()? $lastPage : $this->generateUrl('accueil');

        // If outing not found, show error message then redirect
        if(!$outing){
            $this->addFlash('error', 'Annulation impossible. Cette sortie n\'existe pas');
            return $this->redirect($urlToRedirect);
        }

        $loggedUser = $this->getUser();

        // Check if logged user is the organizer of that outing
        if($outing->getOrganizer() === $loggedUser) {

            $status = $outing->getStatus()->getLibelle();

            // Check if that outing can be cancelled
            if($status === 'Ouverte' || $status === 'Clôturée') {
                $outing->setAbout("");

                $form = $this->createForm(OutingCancelType::class, $outing);
                $form->handleRequest($request);

                // Check if form is submitted and valid
                if($form->isSubmitted() && $form->isValid()) {
                    // Update outing status
                    $cancelledStatus = $statusRepository->findOneByLibelle('Annulée');
                    $outing->setStatus($cancelledStatus);
                    $entityManager->flush();

                    // Send success message and redirect
                    $this->addFlash('success', 'Sortie '.$outing->getName().' annulée.');
                    return $this->redirect($urlToRedirect);
                }

                // Show cancel form
                return $this->render('outing/cancel.html.twig', [
                    'outing' => $outing,
                    'form' => $form->createView(),
                    'urlToRedirect' => $urlToRedirect,
                ]);
            }

            return $this->render('outing/cancel.html.twig', [
                'outing' => $outing,
                'urlToRedirect' => $urlToRedirect,
            ]);
        }
        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
    }

    /**
     * @Route("/sortie/{id}/inscription", name="subscription", requirements={"id"="\d+"})
     */
    public function subscribe(int $id, OutingRepository $outingRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $outing = $outingRepository->find($id);
        /** @var $user User */
        $user = $this->getUser();

        // Define URL to redirect
        $lastPage = $request->headers->get('referer');
        $urlToRedirect = $lastPage && $lastPage != $request->getUri()? $lastPage : $this->generateUrl('accueil');

        // If outing not found, show error message then redirect
        if(!$outing){
            $this->addFlash('error', 'Inscription impossible. Cette sortie n\'existe pas');
            return $this->redirect($urlToRedirect);
        }

        $subcriptionForm = $this->createForm(OutingSubscriptionType::class, $outing);
        $subcriptionForm->handleRequest($request);

        if($subcriptionForm->isSubmitted()){
            $outing->addUser($user);
            $entityManager->persist($outing);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes inscrit sur la sortie ' . $outing->getName());
            return $this->redirect($urlToRedirect);
        }


        return $this->render('outing_subscription/index.html.twig', [
            'outing'=>$outing,
            'subscriptionForm'=>$subcriptionForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/{id}/desistement", name="outing_unregister", requirements={"id"="\d+"})
     */
    public function unregister(int $id, OutingRepository $outingRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $outing = $outingRepository->find($id);
        /** @var $loggedUser User */
        $loggedUser = $this->getUser();

        // Define URL to redirect
        $lastPage = $request->headers->get('referer');
        $urlToRedirect = $lastPage && $lastPage != $request->getUri()? $lastPage : $this->generateUrl('accueil');

        // If outing not found, show error message then redirect
        if(!$outing){
            $this->addFlash('error', 'Désistement impossible. Cette sortie n\'existe pas');
            return $this->redirect($urlToRedirect);
        }

        // Check if logged user is registered to that outing
        if($outing->getUsers()->contains($loggedUser)) {
            $outing->removeUser($loggedUser);
            $entityManager->persist($outing);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes n\'êtes plus inscrit à la sortie ' . $outing->getName());
        } else {
            // If logged user is not registered, show error message
            $this->addFlash('error', 'Désistement impossible. Vous êtes n\'êtes pas inscrit à la sortie ' . $outing->getName());
        }

        // Redirect to last page visited
        return $this->redirect($urlToRedirect);
    }

}