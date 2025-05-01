<?php

namespace App\Controller;

use App\Exception\ReservationTimeAlreadyTaken;
use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservations', name: 'app_reservations_create', methods: ['POST'])]
class ReservationController extends AbstractController
{
    /**
     * @throws ReservationTimeAlreadyTaken
     */
    public function __invoke(Request $request, ReservationService $reservationService): JsonResponse
    {
        return $this->json($reservationService->createReservationFromRequest($request));
    }
}