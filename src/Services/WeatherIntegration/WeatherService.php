<?php

declare(strict_types=1);

namespace App\Services\WeatherIntegration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WeatherService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getCurrentWeather(float $latitude, float $longitude): array
    {
        $response = $this->httpClient->request('GET', 'https://api.open-meteo.com/v1/forecast', [
            'query' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,rain,showers,snowfall,weather_code,cloud_cover,pressure_msl,surface_pressure,wind_speed_10m,wind_direction_10m,wind_gusts_10m',
                'timezone' => 'Europe/Bratislava',
            ],
            'timeout' => 10,
        ]);

        return $this->formatCurrentWeather($response->toArray());
    }

    /**
     * @param array<string, mixed> $weatherData
     * @return array<string, mixed>
     */
    private function formatCurrentWeather(array $weatherData): array
    {
        $current = $weatherData['current'];

        return [
            'current_weather' => [
                'location' => 'Košice',
                'temperature' => [
                    'actual' => $current['temperature_2m'],
                    'feels_like' => $current['apparent_temperature'],
                    'unit' => '°C',
                ],
                'humidity' => [
                    'value' => $current['relative_humidity_2m'],
                    'unit' => '%',
                ],
                'cloudiness' => [
                    'value' => $current['cloud_cover'],
                    'unit' => '%',
                    'weather_code' => $current['weather_code'],
                    'description' => $this->getWeatherDescription($current['weather_code']),
                ],
                'wind' => [
                    'speed' => $current['wind_speed_10m'],
                    'speed_unit' => 'km/h',
                    'direction' => $current['wind_direction_10m'],
                    'direction_unit' => '°',
                    'direction_text' => $this->getWindDirection($current['wind_direction_10m']),
                ],
                'pressure' => [
                    'value' => $current['pressure_msl'],
                    'unit' => 'hPa',
                ],
                'elevation' => [
                    'value' => (int) $weatherData['elevation'],
                    'unit' => 'm',
                ],
            ],
        ];
    }

    private function getWeatherDescription(int $weatherCode): string
    {
        return match ($weatherCode) {
            0 => 'jasno',
            1, 2, 3 => 'oblačno',
            45, 48 => 'hmla',
            51, 53, 55 => 'mrholenie',
            56, 57 => 'mrznúce mrholenie',
            61, 63, 65 => 'dážď',
            66, 67 => 'mrznúci dážď',
            71, 73, 75 => 'sneženie',
            77 => 'snehové vločky',
            80, 81, 82 => 'prehánky',
            85, 86 => 'snehové prehánky',
            95 => 'búrka',
            96, 99 => 'búrka s krúpami',
            default => 'neznáme',
        };
    }

    private function getWindDirection(float $degrees): string
    {
        $directions = [
            'sever', 'severovýchod', 'východ', 'juhovýchod',
            'juh', 'juhozápad', 'západ', 'severozápad',
        ];

        $index = (int) round($degrees / 45) % 8;

        return $directions[$index];
    }
}
