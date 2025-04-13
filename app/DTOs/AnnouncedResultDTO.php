<?php

namespace App\DTOs;

class AnnouncedResultDTO
{
    public function __construct(
        public string $party,
        public int $total_score
    ) {}

    /**
     * Convert a collection of grouped announced results to a DTO array.
     *
     * @param \Illuminate\Support\Collection $announcedResults
     * @return array
     */
    public static function fromGroupedCollection($announcedResults): array
    {
        return $announcedResults->map(function ($score, $party) {
            return new self($party, $score);
        })->values()->toArray();
    }
}
