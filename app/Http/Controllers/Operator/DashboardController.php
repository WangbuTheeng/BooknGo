<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $operator = auth()->user()->operator;
        $busesCount = $operator->buses()->count();
        $routesCount = \App\Models\Route::count();
        $tripsCount = $operator->trips()->count();
        $bookingsCount = $operator->bookings()->count();

        return view('operator.dashboard.index', compact('busesCount', 'routesCount', 'tripsCount', 'bookingsCount'));
    }
}
