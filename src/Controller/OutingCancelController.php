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
        $loggedUser = $this->getUser();

        // Check if logged user is the organizer of that outing
        if($outing->getOrganizer() === $loggedUser) {

            $status = $outing->getStatus()->getLibelle();

            // Check if that outing can be cancelled
            if($status === 'Ouverte' || $status === 'Clôturée') {
                $outing->setAbout("");

                $form = $this->createForm(OutingCancelType::class, $outing);
                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()) {
                    $cancelledStatus = $statusRepository->findOneByLibelle('Annulée');
                    $outing->setStatus($cancelledStatus);
                    $entityManager->flush();

                    // TODO: Redirect to show Outing page
                    return $this->redirectToRoute('show_profile', [
                        'username' => $loggedUser->getNickName()
                    ]);
                }

                return $this->render('outing/cancel.html.twig', [
                    'outing' => $outing,
                    'form' => $form->createView(),
                ]);
            }

            return $this->render('outing/cancel.html.twig', [
                'outing' => $outing,
            ]);
        }

        // TODO: Redirect to show Outing page
        return $this->redirectToRoute('show_profile', ['username' => $loggedUser->getNickName()]);
    }
}
