<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'konsultan'   => redirect()->route('admin.reports'),
            'surveyor'    => redirect()->route('surveyor.tasks'),
            'kementerian' => redirect()->route('kementerian.index'),
            default       => view('masyarakat.dashboard', [
                'reports' => $user->reports()->latest()->get(),
            ]),
        };
    }
}
