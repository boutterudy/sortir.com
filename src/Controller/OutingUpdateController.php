<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Repository\TownRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingUpdateController extends AbstractController
{
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
}
