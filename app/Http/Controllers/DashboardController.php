<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(TaskService $taskService)
    {
        $stats = $taskService->getDashboardStats(auth()->user());
        
        return view('dashboard', $stats);
    }
}
