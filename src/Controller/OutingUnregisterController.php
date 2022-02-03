<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\OutingSubscriptionType;
use App\Repository\OutingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\equalTo;

class OutingUnregisterController extends AbstractController
{
    /**
     * @Route("/sortie/{id}/desistement", name="outing_unregister", requirements={"id"="\d+"})
     */
    public function unregister(int $id, OutingRepository $outingRepository, Request $request, EntityManagerInterface $entityManager): Response
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
}
