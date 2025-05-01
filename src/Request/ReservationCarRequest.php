<?php

namespace App\Request;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

;

class ReservationCarRequest extends Constraint
{
    #[Assert\NotBlank(message: 'Required carId',)]
    private int $carId;

    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private string $userEmail;

    #[Assert\NotBlank(message: 'Required start date',)]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:s', message: 'The date startTime must be in format 2025-04-20T14:00:00')]
    private string $startTime;

    #[Assert\NotBlank(message: 'Required end date',)]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:s', message: 'The date startTime must be in format 2025-04-20T14:00:00')]
    #[Assert\GreaterThan(
        propertyPath: 'startTime',
        message: 'End time must be greater than start time'
    )]
    private string $endTime;

    public function getCarId(): int
    {
        return $this->carId;
    }

    public function setCarId(int $carId): void
    {
        $this->carId = $carId;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }

    public function setEndTime(string $endTime): void
    {
        $this->endTime = $endTime;
    }
}