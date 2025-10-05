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

        return Cache::remember($cacheKey, 3600, function () use ($item) {

            try {
                // Replace with your actual API endpoint and credentials
                // $response = Http::get('https://api.example.com/pollen', [
                //     'city' => $city,
                //     'zipcode' => $zipcode,
                //     'api_key' => config('services.pollen.api_key'),
                // ]);

                // if ($response->successful()) {
                //     return $response->json();
                // }

                // For now, return mock data until you implement the real API
                return $this->getMockPollenData();

            } catch (\Exception $e) {
                // Log the error and return default data
                logger()->error('Pollen API Error: ' . $e->getMessage());
                return $this->getDefaultPollenData();
            }
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
