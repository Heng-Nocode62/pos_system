<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function dailySales(){
        return response()->json($this->reportService->dailySales());
    }
}
