<?php

namespace App\Controller;

use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    /**
     * @Route("/api/place/{idPlace}", name="get_place", requirements={"idPlace"="\d+"})
     */
    public function getPlace($idPlace, PlaceRepository $placeRepository): Response
    {
        $place = $placeRepository->find($idPlace);

            $responseArray[] = array(
                "id" => $place->getId(),
                "street" => $place->getStreet(),
                "postal_code" => $place->getTown()->getPostalCode(),
                "latitude" => $place->getLatitude(),
                "longitude" => $place->getLongitude()
            );
        return new JsonResponse($responseArray);
    }
}
