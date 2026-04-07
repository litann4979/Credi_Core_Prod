<?php

namespace App\Http\Controllers;

class AdminLiveDashboardController extends Controller
{
    public function index()
    {
        return view('admin.LiveDashboard.index');
    }
    public function userindex(){
        return view('admin.LiveDashboard.userindex');
    }
}
