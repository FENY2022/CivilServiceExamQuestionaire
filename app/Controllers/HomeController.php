<?php

namespace App\Controllers;

use App\Models\SettingsModel;

class HomeController extends BaseController
{
    public function index()
    {
        $captchaA = random_int(1, 20);
        $captchaB = random_int(1, 20);
        session()->set('captcha_answer', $captchaA + $captchaB);

        return view('home/index', [
            'title' => config('App')->appName,
            'active' => 'home',
            'guestMode' => (new SettingsModel())->isGuestMode(),
            'captchaA' => $captchaA,
            'captchaB' => $captchaB,
            'message' => session()->getFlashdata('message'),
            'status' => session()->getFlashdata('status') ?? 'info',
        ]);
    }

    public function about(): string
    {
        return view('home/about', [
            'title' => 'About - ' . config('App')->appName,
            'active' => 'about',
        ]);
    }
}
