<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Exception\ReservationTimeAlreadyTaken;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Request\ReservationCarRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ReservationService
{
    public function __construct(
        private SerializerInterface   $serializer,
        private ValidatorInterface    $validator,
        private CarRepository         $carService,
        private EntityManagerInterface   $entityManager,
        private ReservationRepository $reservationRepository,
    )
    {

    }

    /**
     * @throws ReservationTimeAlreadyTaken
     */
    public function createReservationFromRequest(Request $request): Reservation
    {
        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestException('Unsupported content format');
        }
        /**
         * @var ReservationCarRequest $reservationRequest
         */
        $reservationRequest = $this->serializer->deserialize(
            $request->getContent(),
            ReservationCarRequest::class,
            'json'
        );
        $errors = $this->validator->validate($reservationRequest);
        if (count($errors) > 0) {
            /**
             * @var ConstraintViolation $error
             */
            $error = $errors[0];
            throw new BadRequestException($error->getMessage());
        }
        // check if car is exist in database
        if (!($car = $this->carService->find($reservationRequest->getCarId()))) throw new BadRequestException('Card is not exist');

        $startTime = date_create_from_format('Y-m-d\TH:i:s', $reservationRequest->getStartTime());
        $endTime = date_create_from_format('Y-m-d\TH:i:s', $reservationRequest->getEndTime());

        // check if time is already taken
        if ($this->reservationRepository->isTimeAlreadyTaken($startTime, $endTime, $car->getId())) throw new ReservationTimeAlreadyTaken('Time is Already Taken');

        $reservation = new Reservation();
        $reservation->setCar($car)
            ->setEndTime($endTime)
            ->setStartTime($startTime)
            ->setUserEmail($reservationRequest->getUserEmail());

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }
}