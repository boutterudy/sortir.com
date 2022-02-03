<?php

namespace App\Controller;

use App\Form\OutingCancelType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingCancelController extends AbstractController
{
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
}
