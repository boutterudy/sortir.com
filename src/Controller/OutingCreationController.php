<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\Place;
use App\Entity\Town;
use App\Form\OutingType;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use App\Repository\TownRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingCreationController extends AbstractController
{
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
        }

        return $this->render('outing_creation/outing_creation.html.twig', [
            'controller_name' => 'OutingCreationController',
            'outingCreationForm' => $outingCreationForm->createView()
        ]);
    }
}
