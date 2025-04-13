<?php

namespace App\Services;
use App\Repositories\WardRepository;


class WardService
{
    protected WardRepository $wardRepository;

    public function __construct(WardRepository $wardRepository)
    {
        $this->wardRepository = $wardRepository;
    }

    /**
     * Fetch wards based on lga_id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWardsByLga($lgaId)
    {
        return $this->wardRepository->getWardsByLga($lgaId);  // Call repository to fetch wards
    }
}