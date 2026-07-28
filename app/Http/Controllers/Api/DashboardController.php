<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    protected DashboardService $dashboardService;
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(){
        // $data = $this->dashboardService->dashboard();
        try{
            $data = $this->dashboardService->stats();
        return response()->json($data);
        }catch(\Throwable $e){
            return response()->json([
                "message"=>$e->getMessage()
            ]);
        }
        
    }
}
