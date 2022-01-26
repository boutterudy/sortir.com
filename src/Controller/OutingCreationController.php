<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingCreationController extends AbstractController
{
    /**
     * @Route("/sortie/creation", name="outing_creation")
     */
    public function create(Request $request, UserRepository $userRepository): Response
    {
        //$userRepository = $this->getDoctrine()->getRepository(UserRepository::class);

        $outing = new Outing();
        $outingCreationForm = $this->createForm(OutingType::class, $outing);

        //$user = $userRepository->find($this->getUser()->getUserIdentifier());
        //$outing->setCampus($user->getCampus());

        //todo traitement

        return $this->render('outing_creation/outlet_creation.html.twig', [
            'controller_name' => 'OutingCreationController',
            'outingCreationForm' => $outingCreationForm->createView()
        ]);
    }
}
