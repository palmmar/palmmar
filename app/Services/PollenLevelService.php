<?php

    namespace App\Services;

    class PollenLevelService
    {
        private array $data;

        public function __construct(array $data)
        {
            $this->data = $data;
        }

        public function getName(): string
        {
            return $this->data['text'] ?? "Okänd";
        }


        public function getLevel(): string
        {
            return match($this->data['levelSeries']['items']['level'] ?? 0) {
                0 => "Ingen",
                1 => 'Låg',
                2,3 => 'Måttlig',
                4,5 => 'Hög',
                default => 'Mycket hög',
            };
        }

        public function getValue(): int
        {
            return $this->data['levelSeries']['items']['level'] ?? 0;
        }

        public function endOfSeason(): bool
        {
            return $this->data['isEndOfSeason'] ?? false;
        }

        public function getEndOfSeasonData(): array
        {
            return [
                'overall_level' => 'Säsongen avslutad',
                'overall_level_value' => 0,
                'types' => [],
                'last_updated' => null,
                'off_season' => true,
            ];
        }

        public function getColor(): string
        {
            return match($this->data['level'] ?? 0) {
                0, 1 => 'text-green-600',
                2,3 => 'text-yellow-600',
                4,5 => 'text-orange-600',
                default => 'text-red-600'
            };
        }
    }
