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
     * @Route("/sortie/modification/{idOuting}", name="outing_update", requirements={"idOuting"="\d+"})
     */
    public function update(Request                $request,
                           UserRepository         $userRepository,
                           StatusRepository       $statusRepository,
                           OutingRepository       $outingRepository,
                           EntityManagerInterface $manager,
                                                  $idOuting): Response
    {
        $outing = $outingRepository->findFullOuting($idOuting);
        $user = $outing->getOrganizer();

        //TODO
        if($user !== $userRepository->find($this->getUser()->getId()) && !$user->getIsAdmin()){
            //$this->redirect("");
        }

        //last parameter allow pre-selecting the Place ChoiceType
        $outingUpdateForm = $this->createForm(OutingType::class, $outing);

        $outingUpdateForm->handleRequest($request);

        if ($outingUpdateForm->isSubmitted()) {
            if ($outingUpdateForm->get('save')->isClicked()) {
                $outing->setStatus($statusRepository->findOneBy(['libelle' => 'En création']));
            } elseif ($outingUpdateForm->get('publish')->isClicked()) {
                $outing->setStatus($statusRepository->findOneBy(['libelle' => 'Ouverte']));
            } elseif ($outingUpdateForm->get('suppress')->isClicked()) {
                $outing->setStatus($statusRepository->findOneBy(['libelle' => 'Annulée']));
            }
            $manager->persist($outing);
            $manager->flush();

            //TODO
            if ($outingUpdateForm->has('suppress')) {
                if ($outingUpdateForm->get('suppress')->isClicked()) {
                    //$this->redirect("");
                }
            }
        }

        return $this->render('outing_update/outing_update.html.twig', [
            'outingUpdateForm' => $outingUpdateForm->createView(),
            'outing' => $outing
        ]);
    }
}
