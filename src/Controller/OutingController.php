<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Entity\User;
use App\Form\OutingCancelType;
use App\Form\OutingsFilterType;
use App\Form\OutingType;
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
    public function home(OutingRepository $outingRepository,
                         UserRepository $userRepository,
                         Request $request,
                         EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OutingsFilterType::class);
        $form->handleRequest($request);
        $subscribed = null;
        $unsubscribed = null;
        $start = null;
        $stop = null;


        if ($form->isSubmitted() && $form->isValid())
        {
            $name = $form['name']->getData();
            $campus = $form['campus']->getData();

            //TODO -> filter by date
         //   $start = $form['startAt']->getData();
         //  $stop = $form['limitSubscriptionDate']->getData();

            $organizer = $form['organizer']->getData();

            //TODO
         //   $subscribed = $form['subscribed']->getData();
         //   $unsubscribed = $form['unsubscribed']->getData();

            $passed = $form['passed']->getData();
            $user = $userRepository->findOneById($this->getUser()->getId());
            $outingsList = $outingRepository->outingFilter($user, $name, $organizer, $campus, $start, $stop, $subscribed, $unsubscribed, $passed);

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
   public function display(OutingRepository $outingRepository,
                           UserRepository $userRepository,
                           int $id,
                           Request $request){
       $outing = $outingRepository->find($id);

       // Define URL to redirect
       $lastPage = $request->headers->get('referer');
       $urlToRedirect = $lastPage && $lastPage != $request->getUri()? $lastPage : $this->generateUrl('accueil');

       // If outing not found, show error message then redirect
       if(!$outing){
           $this->addFlash('error', 'Affichage impossible. Cette sortie n\'existe pas');
           return $this->redirect($urlToRedirect);
       }
       $user = $userRepository->findAll();
       return $this ->render('outing/display.html.twig',[
          'outing'=>$outing,
           'user'=>$user
       ]);
   }

    /**
     * @Route("/sortie/{id}/annuler", name="outing_cancel", requirements={"id"="\d+"})
     */
    public function index(int $id,
                          EntityManagerInterface $entityManager,
                          Request $request,
                          OutingRepository $outingRepository,
                          StatusRepository $statusRepository): Response
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
    public function subscribe(int $id,
                              OutingRepository $outingRepository,
                              Request $request,
                              EntityManagerInterface $entityManager): Response
    {
        /** @var Outing $outing */
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
        }elseif(!$outing->getUsers()->contains($user) && $outing->getStatus()->getLibelle() == 'Ouverte'){
            $outing->addUser($user);
            $entityManager->persist($outing);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes inscrit sur la sortie ' . $outing->getName());
            return $this->redirect($urlToRedirect);
        }elseif ($outing->getUsers()->contains($user)){
            $this->addFlash('success', 'Vous êtes déjà inscrit sur cette sortie ' . $outing->getName());
            return $this->redirect($urlToRedirect);
        }elseif ($outing->getStatus()->getLibelle() != 'Ouverte'){
            $this->addFlash('success', 'Cette sortie n\'est pas ouverte à l\'inscription ! ' . $outing->getName());
            return $this->redirect($urlToRedirect);
        }else{
            $this->addFlash('success', 'Une erreur empêche votre inscription' . $outing->getName());
            return $this->redirect($urlToRedirect);
        }
    }

    /**
     * @Route("/sortie/{id}/desistement", name="outing_unregister", requirements={"id"="\d+"})
     */
    public function unregister(int $id,
                               OutingRepository $outingRepository,
                               Request $request,
                               EntityManagerInterface $entityManager): Response
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

    /**
     * @Route("/sortie/creation", name="outing_creation")
     */
    public function create(Request $request,
                           UserRepository $userRepository,
                           StatusRepository $statusRepository,
                           EntityManagerInterface $manager): Response
    {
        $outing = new Outing();
        $user = $userRepository->find($this->getUser()->getId());

        $outing->setOrganizer($user);

        $outing->setCampus($user->getCampus());

        $outingCreationForm = $this->createForm(OutingType::class, $outing);

        $outingCreationForm->handleRequest($request);

        if ($outingCreationForm->isSubmitted()) {
            if($outingCreationForm->get('save')->isClicked()){
                $outing->setStatus($statusRepository->findOneBy(['libelle'=>'En création']));
            }elseif($outingCreationForm->get('publish')->isClicked()) {
                $outing->setStatus($statusRepository->findOneBy(['libelle'=>'Ouverte']));
            }
            $manager->persist($outing);
            $manager->flush();

            $url = $this->generateUrl('outing_details', ['id' => $outing->getId()]);
            return $this->redirect($url);
        }

        return $this->render('outing_creation/outing_creation.html.twig', [
            'outingCreationForm' => $outingCreationForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/{idOuting}/modification", name="outing_update", requirements={"idOuting"="\d+"})
     */
    public function update(Request                $request,
                           UserRepository         $userRepository,
                           StatusRepository       $statusRepository,
                           OutingRepository       $outingRepository,
                           EntityManagerInterface $em,
                                                  $idOuting): Response
    {
        $outing = $outingRepository->findFullOuting($idOuting);
        $organizer = $outing->getOrganizer();
        $loggedUser = $this->getUser();

        if($organizer !== $loggedUser && !$loggedUser->getIsAdmin()){
            $this->addFlash('error', 'Vous n\'avez pas les droits pour modifier cette sortie!');
            return $this->redirect($this->generateUrl('accueil'));
        }

        if($outing->getStatus()->getLibelle() !== 'En création'){
            $this->addFlash('error', 'Cette sortie n\'est plus modifiable');
            return $this->redirect($this->generateUrl('accueil'));
        }

        //last parameter allow pre-selecting the Place ChoiceType
        $outingUpdateForm = $this->createForm(OutingType::class, $outing);

        $outingUpdateForm->handleRequest($request);

        if ($outingUpdateForm->isSubmitted()) {
            if ($outingUpdateForm->get('save')->isClicked()) {
                $this->addFlash('success', 'Sortie enregistrée. Pensez à la publier!');
                $outing->setStatus($statusRepository->findOneBy(['libelle' => 'En création']));
                $em->persist($outing);
            } elseif ($outingUpdateForm->get('publish')->isClicked()) {
                $this->addFlash('success', 'Sortie publiée');
                $outing->setStatus($statusRepository->findOneBy(['libelle' => 'Ouverte']));
                $em->persist($outing);
            } elseif ($outingUpdateForm->get('confirm_suppress')){
                $this->addFlash('success', 'Sortie supprimée');
                $em->remove($outing);
            }

            $em->flush();

            if ($outingUpdateForm->get('save')->isClicked() || $outingUpdateForm->get('publish')->isClicked()) {
                $url = $this->generateUrl('outing_details', ['id' => $outing->getId()]);
                return $this->redirect($url);
            }

            //if form is submit but not redirected at this point, it was a suppression.
            return $this->redirect($this->generateUrl('accueil'));
        }

        return $this->render('outing_update/outing_update.html.twig', [
            'outingUpdateForm' => $outingUpdateForm->createView(),
            'outing' => $outing
        ]);
    }

    /**
     * @Route("/sortie/{id}/publier", name="outing_publish", requirements={"id"="\d+"})
     */
    public function publish(int $id,
                               OutingRepository $outingRepository,
                               StatusRepository $statusRepository,
                               EntityManagerInterface $entityManager): Response
    {
        $outing = $outingRepository->findFullOuting($id);
        $organizer = $outing->getOrganizer();
        $loggedUser = $this->getUser();

        if($organizer !== $loggedUser && !$loggedUser->getIsAdmin()){
            $this->addFlash('error', 'Vous n\'avez pas les droits pour publier cette sortie!');
            return $this->redirect($this->generateUrl('accueil'));
        }

        if($outing->getStatus()->getLibelle() == 'En création'){
            $statusPublished = $statusRepository->findOneBy(['libelle'=>'Ouverte']);
            $outing->setStatus($statusPublished);
            $entityManager->persist($outing);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été publiée !');
        }
        return $this->redirectToRoute('outing_details', [
            'id'=>$id
        ]);
    }
}
