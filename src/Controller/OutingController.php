<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Form\OutingsFilterType;
use App\Repository\OutingRepository;
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
     * @Route("/sortie/detail/{id}", name="outing_details", requirements={"id"="\d+"})
     */

   public function display(OutingRepository $outingRepository, UserRepository $userRepository, int $id){
       $outing = $outingRepository->find($id);
       $user = $userRepository->findAll();
       return $this ->render('outing/display.html.twig',[
          'outing'=>$outing,
           'user'=>$user
       ]);
   }


}