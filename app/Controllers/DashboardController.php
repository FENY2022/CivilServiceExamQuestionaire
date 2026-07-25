<?php

namespace App\Controllers;

use App\Libraries\Auth;

class DashboardController extends BaseController
{
    public function index()
    {
        $redirect = (new Auth())->requireLogin();
        if ($redirect) {
            return $redirect;
        }

        return view('dashboard/index', [
            'title' => 'Dashboard - ' . config('App')->appName,
            'active' => 'dashboard',
            'name' => (new Auth())->currentUserName(),
            'isGuest' => session()->has('guest'),
        ]);
    }
}
