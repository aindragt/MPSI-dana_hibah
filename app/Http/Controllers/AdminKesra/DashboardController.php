<?php

namespace App\Http\Controllers\AdminKesra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('AdminKesra/Dashboard');
    }
}
