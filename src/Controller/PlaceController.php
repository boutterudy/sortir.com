<?php

namespace App\Controller;

use App\Repository\PlaceRepository;
use App\Repository\TownRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    /**
     * @Route("/api/place/{id}", name="get_place", requirements={"id"="\d+"})
     * @param $id
     * @param PlaceRepository $placeRepository
     * @return Response
     */
    public function getPlace($id, PlaceRepository $placeRepository): Response
    {
        $place = $placeRepository->find($id);

        $responseArray[] = array(
                "id" => $place->getId(),
                "street" => $place->getStreet(),
                "postal_code" => $place->getTown()->getPostalCode(),
                "latitude" => $place->getLatitude(),
                "longitude" => $place->getLongitude()
            );
        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/api/town/{id}/places", name="outing_list_places", requirements={"id"="\d+"})
     * @param PlaceRepository $placeRepository
     * @param TownRepository $townRepository
     * @param null $id
     * @return void
     */
    public function listPlacesOfTownAction(
        PlaceRepository $placeRepository,
        TownRepository $townRepository,
        $id = null
    ): Response
    {
        // Search the places that belongs to the town
        if ($id) {
            $places = $placeRepository->findByTown($townRepository->findOneBy(['id' => $id]));
        } else {
            $places = $placeRepository->findByTown();
        }

        // Serialize into an array the data that we need
        $responseArray = array();
        foreach ($places as $place) {
            $responseArray[] = array(
                "id" => $place->getId(),
                "name" => $place->getName()
            );
        }
        return new JsonResponse($responseArray);
    }
}
