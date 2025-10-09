<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PollenService
{
    /**
     * Get pollen data for a specific location
     *
     * @param string $city
     * @param string $zipcode
     * @return array
     */
    public function getPollenData(string $city = ""): array
    {
        $cacheKey = "pollen_data_{$city}";

        $region = $this->getPollenRegions();

        $item = collect($region)->firstWhere('name', $city);




        if (!$item) {
            return $this->getDefaultPollenData();
        }

        $url = $item['forecasts'] ?? null;

        $response = http::get($url)->json();
        $pollen_types = $response['items'] ?? [];

        return Cache::remember($cacheKey, 3600, function () use ($pollen_types)
        {
            $pollen_data = [];
            foreach ($pollen_types as $type)
            {
                $pollen_level = new PollenLevelService($type);
                if ($pollen_level->endOfSeason()){
                    return $pollen_level->getEndOfSeasonData();
                }

                $pollen_data['types'][] = [
                    'name' => $pollen_level->getName(),
                    'level' => $pollen_level->getLevel(),
                    'value' => $pollen_level->getValue(),
                    'color' => $pollen_level->getColor(),
                ];
            }

            return $pollen_data;
        });

    }

    private function getPollenRegions(): array
    {
        $cacheKey = "pollen_regions";


        return Cache::remember($cacheKey, 3600, function () {
            $response = Http::get("https://api.pollenrapporten.se/v1/regions");

            $data = $response->json();

            return $data['items'] ?? [];
        });

    }

    /**
     * Mock data for testing (remove this when you implement the real API)
     */
    private function getMockPollenData(): array
    {
        return [
            'overall_level' => 'Moderate',
            'overall_level_value' => 3,
            'types' => [
                [
                    'name' => 'Tree Pollen',
                    'level' => 'High',
                    'value' => 4,
                    'color' => 'text-orange-600'
                ],
                [
                    'name' => 'Björk',
                    'level' => 'High',
                    'value' => 5,
                    'color' => 'text-orange-600'
                ],
                [
                    'name' => 'Grass Pollen',
                    'level' => 'Low',
                    'value' => 2,
                    'color' => 'text-green-600'
                ],
                [
                    'name' => 'Weed Pollen',
                    'level' => 'Moderate',
                    'value' => 3,
                    'color' => 'text-yellow-600'
                ],
            ],
            'last_updated' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Default data when API fails
     */
    private function getDefaultPollenData(): array
    {
        return [
            'overall_level' => 'Unknown',
            'overall_level_value' => 0,
            'types' => [],
            'last_updated' => null,
            'error' => true,
        ];
    }
}
