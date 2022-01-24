<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingCreationController extends AbstractController
{
    /**
     * @Route("/outing/creation", name="outing_creation")
     */
    public function create(): Response
    {
        return $this->render('outing_creation/outlet_creation.html.twig', [
            'controller_name' => 'OutingCreationController',
        ]);
    }
}
