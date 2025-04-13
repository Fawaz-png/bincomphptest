<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WardService;
class WardController extends Controller
{
    //
    protected WardService $wardService;
    public function __construct(WardService $wardService)
    {
        $this->wardService = $wardService;
    }

    public function index(Request $request)
    {
        $lgaId = $request->get('lga_id');

        // Fetch Wards using WardService
        $wards = $this->wardService->getWardsByLga($lgaId);

        return response()->json($wards);
    }
}
