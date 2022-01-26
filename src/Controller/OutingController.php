<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\User;
use App\Form\OutingsFilterType;
use App\Repository\OutingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OutingController extends AbstractController
{


    /**
     * @Route("/accueil", name="accueil")
     */

    public function home(OutingRepository $outingRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(OutingsFilterType::class, null);

        $form->handleRequest($request);
        $registered = null;
        $notRegistered = null;
        $outingsList = null;
        if ($form->isSubmitted() && $form->isValid()) {

            $place = $form['lieu']->getData();


            $start = $form['start']->getData();
            $stop = $form['stop']->getData();

            $organizer = $form['organizer']->getData();

            $registered = $form['registered']->getData();
            $notRegistered = $form['notRegistered']->getData();
            $passed = $form['passed']->getData();
            $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
            $outingsList = $outingRepository->outingFilter($user, $place,$organizer , $start, $stop, $passed);
        }else{
            $outingsList = $entityManager->getRepository(Outing::class)->findAll();
        }

        return $this->render('home.html.twig', [
            'notRegistered' => $notRegistered,
            'registered' => $registered,
            'app_name' => 'Outings',
            'form' => $form->createView(),
            'outings' => $outingsList,
        ]);
    }



}