<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\User;
use App\Form\OutingSubscriptionType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Services\OutingStatusManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingSubscriptionController extends AbstractController
{
    /**
     * @Route("/sortie/inscription/{id}", name="subscription", requirements={"id"="\d+"})
     */
    public function subscribe(int $id, OutingRepository $outingRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $outing = $outingRepository->find($id);
        /** @var $user User */
        $user = $this->getUser();
        if(!$outing){
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }
        $subcriptionForm = $this->createForm(OutingSubscriptionType::class, $outing);
        $subcriptionForm->handleRequest($request);

        if($subcriptionForm->isSubmitted()){
            $outing->addUser($user);
            $entityManager->persist($outing);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes inscrit sur la sortie ' . $outing->getName());
            return $this->redirectToRoute('temporary');
        }


        return $this->render('outing_subscription/index.html.twig', [
            'outing'=>$outing,
            'subscriptionForm'=>$subcriptionForm->createView()
        ]);
    }
}
