<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $nama = Auth::user();
        $data = [
            'titlePage' => 'DASHBOARD'
        ];

        return view('dashboard', compact('nama','data'));
    }
}
