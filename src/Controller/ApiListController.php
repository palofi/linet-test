<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\WeatherIntegration\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiListController extends AbstractController
{
    public function __construct(
        private readonly WeatherService $weatherService
    ) {
    }

    #[Route('/api-list', name: 'api_list', methods: ['GET'])]
    public function apiList(): JsonResponse
    {
        try {
            $latitude = 48.7139;
            $longitude = 21.2581;

            $weatherData = $this->weatherService->getCurrentWeather($latitude, $longitude);

            $result = [
                'source' => 'Open-Meteo.com via WeatherService',
                'timestamp' => date('Y-m-d H:i:s'),
                ...$weatherData,
            ];

            return $this->json($result);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s'),
            ], 500);
        }
    }
}
