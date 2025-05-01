<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReservationControllerTest extends WebTestCase
{
    private array $reservationData = [
        'carId' => 3,
        'userEmail' => 'alice@example.com',
        'startTime' => '2025-05-20T09:00:00',
        'endTime' => '2025-05-20T12:00:00'
    ];

    public function testCreateReservation(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->reservationData)
        );

        // Check that the response is successful (code 200 or 201)
        $this->assertResponseIsSuccessful();

        // Check that the response is in JSON format
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        // Decode the JSON response
        $responseData = json_decode($client->getResponse()->getContent(), true);

        // Check that the response contains the expected data
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($this->reservationData['userEmail'], $responseData['userEmail']);
        $this->assertEquals(new \DateTime($this->reservationData['startTime']), new \DateTime($responseData['startTime']));
        $this->assertEquals(new \DateTime($this->reservationData['endTime']), new \DateTime($responseData['endTime']));
    }

    public function testCreateReservationWithInvalidData(): void
    {
        $client = static::createClient();

        // Incomplete reservation data
        $invalidData = [
            'carId' => 3,
            'userEmail' => 'alice@example.com',
            // Missing startTime and endTime
        ];

        $client->request(
            'POST',
            'api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateReservationWithEndTimeGreaterThanStartTime(): void
    {
        $client = static::createClient();

        $invalidData = $this->reservationData;
        $invalidData['endTime'] = '2025-04-19T09:00:00';

        $client->request(
            'POST',
            'api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateReservationWithInvalidEmail(): void
    {
        $client = static::createClient();

        $invalidData = $this->reservationData;
        $invalidData['userEmail'] = 'xxxxxxxxxxxx';

        $client->request(
            'POST',
            'api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateReservationWithTimeAlreadyTaken(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->reservationData)
        );

        // Check that the response is an error (code 409 Conflict)
        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        // Check that the error message contains information about the conflict
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertStringContainsString('Time is Already Taken', $responseData['error']);
    }
}
