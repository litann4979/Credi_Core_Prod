<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class AdminAccessChoiceController extends Controller
{
    public function index()
    {
        return view('admin.access-choice');
    }

    public function openTvMode(): RedirectResponse
    {
        $tvDashboardUrl = URL::temporarySignedRoute(
            'live-dashboard.user.index',
            now()->addHours(12)
        );

        return redirect()->to($tvDashboardUrl);
    }
}
